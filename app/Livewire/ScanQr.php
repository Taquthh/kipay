<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Merchant;
use App\Models\Wallet;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ScanQr extends Component
{
    public $step = 1; // 1: Scan/Input Payload, 2: Input Nominal & PIN, 3: Sukses
    public $payload;
    public $amount;
    public $pin;

    public $merchant;
    public $errorMessage = '';

    // Langkah 1: Simulasi Scan QR
    public function scan()
    {
        $this->validate(['payload' => 'required|string']);

        $this->merchant = Merchant::where('qr_code_payload', $this->payload)->first();

        if ($this->merchant) {
            // Cek agar user tidak membayar ke tokonya sendiri (opsional)
            if ($this->merchant->user_id == Auth::id()) {
                $this->errorMessage = 'Tidak dapat melakukan pembayaran ke toko sendiri.';
                return;
            }

            $this->errorMessage = '';
            $this->step = 2; // Lanjut ke input nominal
        } else {
            $this->errorMessage = 'QR Code tidak valid atau Merchant tidak ditemukan.';
        }
    }

    // Langkah 2: Proses Pembayaran (PESSIMISTIC LOCKING)
    public function pay()
    {
        $this->validate([
            'amount' => 'required|numeric|min:1000',
            'pin' => 'required|numeric|digits:6'
        ]);

        $user = Auth::user();

        // 1. Verifikasi PIN
        if (!Hash::check($this->pin, $user->pin)) {
            $this->errorMessage = 'PIN salah!';
            return;
        }

        $this->errorMessage = '';

        try {
            // 2. Mulai Database Transaction & Locking
            DB::transaction(function () use ($user) {
                // KUNCI BARIS SALDO PENGIRIM (Pembeli)
                $senderWallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first();

                // KUNCI BARIS SALDO PENERIMA (Pemilik Merchant)
                $receiverWallet = Wallet::where('user_id', $this->merchant->user_id)->lockForUpdate()->first();

                // 3. Validasi Saldo (Mencegah Minus)
                if ($senderWallet->balance < $this->amount) {
                    throw new \Exception('Saldo Anda tidak mencukupi.');
                }

                // 4. Lakukan Mutasi Saldo
                $senderWallet->balance -= $this->amount;
                $senderWallet->save();

                $receiverWallet->balance += $this->amount;
                $receiverWallet->save();

                // 5. Rekam Transaksi Pembeli
                Transaction::create([
                    'reference_id' => 'PAY-' . time() . '-' . $user->id,
                    'user_id' => $user->id,
                    'merchant_id' => $this->merchant->id,
                    'type' => 'payment',
                    'amount' => $this->amount,
                    'description' => 'Pembayaran ke Merchant: ' . $this->merchant->merchant_name, // <--- TAMBAHKAN INI
                    'latest_balance' => $senderWallet->balance,
                    'status' => 'success',
                ]);

                // 6. Rekam Transaksi Penerima (Pemilik Merchant)
                Transaction::create([
                    'reference_id' => 'RCV-' . time() . '-' . $this->merchant->user_id,
                    'user_id' => $this->merchant->user_id,
                    'merchant_id' => $this->merchant->id,
                    'type' => 'receive', // Tipe menerima dana
                    'amount' => $this->amount,
                    'description' => 'Pembayaran masuk dari: ' . $user->name, // <--- TAMBAHKAN INI
                    'latest_balance' => $receiverWallet->balance,
                    'status' => 'success',
                ]);

                // TODO: Panggil Job Queue untuk Notifikasi WA di sini nantinya
            });

            // 1. Pesan untuk Pembeli
            $pesanPembeli = "Pembayaran Rp " . number_format($this->amount, 0, ',', '.') . " ke Merchant *{$this->merchant->merchant_name}* BERHASIL.";
            \App\Jobs\SendWaNotification::dispatch($user->whatsapp, $pesanPembeli);

            // 2. Pesan untuk Pemilik Merchant
            // Mengambil nomor WA pemilik merchant melalui relasi user()
            $pesanMerchant = "Toko *{$this->merchant->merchant_name}* menerima pembayaran masuk sebesar Rp " . number_format($this->amount, 0, ',', '.') . " dari *{$user->name}*.";
            \App\Jobs\SendWaNotification::dispatch($this->merchant->user->whatsapp, $pesanMerchant);
            // Jika sukses melewati DB::transaction, masuk ke step 3
            $this->step = 3;
        } catch (\Exception $e) {
            // Tangkap error jika saldo kurang atau terjadi masalah database
            $this->errorMessage = $e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.scan-qr');
    }
}
