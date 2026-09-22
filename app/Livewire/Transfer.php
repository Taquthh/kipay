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

    public function checkUser()
    {
        $this->validate(['whatsapp' => 'required|numeric']);

        $this->targetUser = User::where('whatsapp', $this->whatsapp)->first();

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

    public function processTransfer()
    {
        $this->validate([
            'amount' => 'required|numeric|min:1000',
            'pin' => 'required|numeric|digits:6'
        ]);

        $user = Auth::user();

        if (!Hash::check($this->pin, $user->pin)) {
            $this->errorMessage = 'PIN salah!';
            return;
        }

        try {
            DB::transaction(function () use ($user) {
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
                    'reference_id' => 'TRF-OUT-' . time(),
                    'user_id' => $user->id,
                    'type' => 'transfer',
                    'amount' => $this->amount,
                    'description' => 'Transfer ke: ' . $this->targetUser->name . ' (' . $this->targetUser->whatsapp . ')', // <--- TAMBAHKAN INI
                    'latest_balance' => $senderWallet->balance,
                    'status' => 'success',
                ]);

                // Rekam Transaksi Penerima (Masuk)
                Transaction::create([
                    'reference_id' => 'TRF-IN-' . time(),
                    'user_id' => $this->targetUser->id,
                    'type' => 'receive',
                    'amount' => $this->amount,
                    'description' => 'Transfer dari: ' . $user->name . ' (' . $user->whatsapp . ')', // <--- TAMBAHKAN INI
                    'latest_balance' => $receiverWallet->balance,
                    'status' => 'success',
                ]);
            });

            // PANGGIL BACKGROUND JOB UNTUK NOTIFIKASI
            $pesanPengirim = "Transfer Rp " . number_format($this->amount, 0, ',', '.') . " ke {$this->targetUser->name} BERHASIL.";
            $pesanPenerima = "Anda menerima dana masuk Rp " . number_format($this->amount, 0, ',', '.') . " dari {$user->name}.";

            \App\Jobs\SendWaNotification::dispatch($user->whatsapp, $pesanPengirim);
            \App\Jobs\SendWaNotification::dispatch($this->targetUser->whatsapp, $pesanPenerima);

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
