<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class Transfer extends Component
{
    public $step = 1;
    public $whatsapp;
    public $amount;
    public $pin;

    public $targetUser;
    public $errorMessage = '';
    public ?string $lastRef = null;

    public function checkUser()
    {
        $this->validate(['whatsapp' => 'required']);

        $normalized = $this->normalizeWhatsapp($this->whatsapp);

        if (! $normalized) {
            $this->errorMessage = 'Format nomor WhatsApp tidak valid.';
            return;
        }

        $this->whatsapp = $normalized;
        $this->targetUser = User::where('whatsapp', $normalized)->first();

        if ($this->targetUser) {
            if ($this->targetUser->id == Auth::id()) {
                $this->errorMessage = 'Tidak bisa transfer ke nomor sendiri.';
                return;
            }
            $this->errorMessage = '';
            $this->step = 2;
        } else {
            $this->errorMessage = 'Nomor WhatsApp tidak terdaftar di sistem.';
        }
    }

    /**
     * Menormalkan nomor ke format 62xxxxxxxxxx, apa pun cara pengguna mengetiknya
     * (diawali 0, 62, +62, atau langsung tanpa kode negara).
     */
    private function normalizeWhatsapp(string $value): ?string
    {
        $digits = preg_replace('/\D/', '', $value);

        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, '620')) {
            // Salah ketik "62" + "0..." -> buang nol tambahan setelah kode negara
            $digits = '62' . substr($digits, 3);
        } elseif (str_starts_with($digits, '62')) {
            // sudah dalam format 62xxxx
        } elseif (str_starts_with($digits, '0')) {
            $digits = '62' . substr($digits, 1);
        } else {
            $digits = '62' . $digits;
        }

        return (strlen($digits) >= 10 && strlen($digits) <= 15) ? $digits : null;
    }

    public function processTransfer()
    {
        $this->validate([
            'amount' => 'required|numeric|min:1000',
            'pin' => 'required|numeric|digits:6'
        ]);

        $user = Auth::user();

        if (! $user->pin || ! Hash::check($this->pin, $user->pin)) {
            $this->errorMessage = 'PIN salah!';
            $this->reset('pin');
            return;
        }

        $this->errorMessage = '';
        $refBase = 'TRF-' . now()->format('ymdHis') . '-' . $user->id . '-' . random_int(100, 999);

        try {
            DB::transaction(function () use ($user, $refBase) {
                // LOCKING: Kunci dompet pengirim dan penerima
                $senderWallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first();
                $receiverWallet = Wallet::where('user_id', $this->targetUser->id)->lockForUpdate()->first();

                if ($senderWallet->balance < $this->amount) {
                    throw new \Exception('Saldo Anda tidak mencukupi.');
                }

                // Mutasi Saldo
                $senderWallet->balance -= $this->amount;
                $senderWallet->save();

                $receiverWallet->balance += $this->amount;
                $receiverWallet->save();

                // Rekam Transaksi Pengirim (Keluar)
                Transaction::create([
                    'reference_id' => $refBase . '-OUT',
                    'user_id' => $user->id,
                    'type' => 'transfer',
                    'amount' => $this->amount,
                    'description' => 'Transfer ke: ' . $this->targetUser->name . ' (' . $this->targetUser->whatsapp . ')',
                    'latest_balance' => $senderWallet->balance,
                    'status' => 'success',
                ]);

                // Rekam Transaksi Penerima (Masuk)
                Transaction::create([
                    'reference_id' => $refBase . '-IN',
                    'user_id' => $this->targetUser->id,
                    'type' => 'receive',
                    'amount' => $this->amount,
                    'description' => 'Transfer dari: ' . $user->name . ' (' . $user->whatsapp . ')',
                    'latest_balance' => $receiverWallet->balance,
                    'status' => 'success',
                ]);
            });

            // PANGGIL BACKGROUND JOB UNTUK NOTIFIKASI
            $pesanPengirim = "Transfer Rp " . number_format($this->amount, 0, ',', '.') . " ke {$this->targetUser->name} BERHASIL.";
            $pesanPenerima = "Anda menerima dana masuk Rp " . number_format($this->amount, 0, ',', '.') . " dari {$user->name}.";

            \App\Jobs\SendWaNotification::dispatch($user->whatsapp, $pesanPengirim);
            \App\Jobs\SendWaNotification::dispatch($this->targetUser->whatsapp, $pesanPenerima);

            $this->lastRef = $refBase;
            $this->reset('pin');
            $this->step = 3;
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.transfer');
    }
}
