<?php

namespace App\Livewire;

use App\Jobs\SendWaNotification;
use App\Models\Merchant;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\On;
use Livewire\Component;

class ScanQr extends Component
{
    public int $payStep = 0;
    public string $payMode = '';
    public string $payload = '';
    public string $manualPayload = '';

    public $payAmount;
    public string $pin = '';
    public string $note = '';

    public ?array $payTarget = null;
    public string $errorMessage = '';
    public ?string $lastRef = null;

    public $targetMerchantId = null;
    public $targetUserId = null;

    /**
     * Menerima payload dari event Livewire.dispatch('scan', { raw: code.data }) di AlpineJS
     */
    #[On('scan')]
    public function scan($raw = null): void
    {
        $this->payload = trim((string) ($raw ?? $this->payload));
        $this->errorMessage = '';

        if ($this->payload === '') {
            $this->errorMessage = 'QR tidak terbaca. Silakan coba lagi.';
            return;
        }

        // 1. Logika Pembayaran ke Merchant (Format: KIPAY-MCH-{id}-{timestamp})
        if (str_starts_with($this->payload, 'KIPAY-MCH-')) {
            $merchant = Merchant::where('qr_code_payload', $this->payload)->first();

            if (! $merchant) {
                $this->errorMessage = 'QR Code tidak valid atau merchant tidak ditemukan.';
                return;
            }
            if ($merchant->user_id == Auth::id()) {
                $this->errorMessage = 'Tidak dapat melakukan pembayaran ke toko sendiri.';
                return;
            }

            $this->payMode = 'merchant';
            $this->targetMerchantId = $merchant->id;
            $this->targetUserId = $merchant->user_id;
            $this->payTarget = [
                'title'    => $merchant->merchant_name,
                'subtitle' => 'Merchant KiPay',
                'initial'  => strtoupper(mb_substr($merchant->merchant_name, 0, 1)),
            ];

            // Lanjut ke Step 1 (Formulir Nominal & PIN)
            $this->payStep = 1;
            return;
        }

        // 2. Logika Transfer P2P / Antar Teman (Format: KIPAY-USR-{id})
        if (str_starts_with($this->payload, 'KIPAY-USR-')) {
            $userId = (int) str_replace('KIPAY-USR-', '', $this->payload);
            $target = User::find($userId);

            if (! $target) {
                $this->errorMessage = 'Pengguna tidak ditemukan.';
                return;
            }
            if ($target->id == Auth::id()) {
                $this->errorMessage = 'Tidak dapat mentransfer saldo ke akun sendiri.';
                return;
            }

            $this->payMode = 'friend';
            $this->targetUserId = $target->id;
            $this->payTarget = [
                'title'    => $target->name,
                'subtitle' => $target->whatsapp,
                'initial'  => strtoupper(mb_substr($target->name, 0, 1)),
            ];

            // Lanjut ke Step 1 (Formulir Nominal & PIN)
            $this->payStep = 1;
            return;
        }

        $this->errorMessage = 'Format QR Code tidak dikenali oleh sistem KiPay.';
    }

    public function quickManualScan(): void
    {
        $value = trim($this->manualPayload);
        $this->manualPayload = '';
        $this->scan($value);
    }

    public function resetScan(): void
    {
        $this->reset(['payload', 'payAmount', 'pin', 'note', 'payTarget', 'lastRef', 'targetMerchantId', 'targetUserId']);
        $this->payStep = 0;
        $this->payMode = '';
        $this->errorMessage = '';
        $this->resetValidation();
    }

    /**
     * Eksekusi transaksi dengan keamanan Database Transaction & Pessimistic Locking
     */
    public function pay(): void
    {
        $this->validate([
            'payAmount' => 'required|numeric|integer|min:1000',
            'pin'       => 'required|digits:6',
            'note'      => 'nullable|string|max:60',
        ], [], ['payAmount' => 'nominal']);

        $user = Auth::user();

        // Validasi PIN
        if (! $user->pin || ! Hash::check($this->pin, $user->pin)) {
            $this->errorMessage = 'PIN transaksi salah.';
            $this->reset('pin');
            return;
        }

        $this->errorMessage = '';
        $merchant   = $this->targetMerchantId ? Merchant::find($this->targetMerchantId) : null;
        $receiverId = $this->targetUserId;
        $refBase    = ($this->payMode === 'merchant' ? 'PAY-' : 'TRF-') . now()->format('ymdHis') . '-' . $user->id . '-' . random_int(100, 999);

        try {
            DB::transaction(function () use ($user, $merchant, $receiverId, $refBase) {
                // Kunci (Lock) baris saldo pengirim dan penerima untuk mencegah race-condition/saldo minus
                $senderWallet   = Wallet::where('user_id', $user->id)->lockForUpdate()->first();
                $receiverWallet = Wallet::where('user_id', $receiverId)->lockForUpdate()->first();

                if (! $senderWallet || ! $receiverWallet) {
                    throw new \Exception('Sistem gagal menemukan data dompet.');
                }
                if ($senderWallet->balance < $this->payAmount) {
                    throw new \Exception('Saldo KiPay Anda tidak mencukupi.');
                }

                // Mutasi Saldo
                $senderWallet->decrement('balance', $this->payAmount);
                $receiverWallet->increment('balance', $this->payAmount);

                $suffix  = $this->note ? ' (' . $this->note . ')' : '';
                $outDesc = $merchant ? 'Pembayaran ke Merchant: ' . $merchant->merchant_name . $suffix : 'Transfer ke ' . $this->payTarget['title'] . $suffix;
                $inDesc  = $merchant ? 'Pembayaran masuk dari: ' . $user->name . $suffix : 'Transfer dari ' . $user->name . $suffix;

                // Riwayat Pengirim
                Transaction::create([
                    'reference_id'   => $refBase . '-OUT',
                    'user_id'        => $user->id,
                    'merchant_id'    => $merchant?->id,
                    'type'           => $merchant ? 'payment' : 'transfer',
                    'amount'         => $this->payAmount,
                    'description'    => $outDesc,
                    'latest_balance' => $senderWallet->balance,
                    'status'         => 'success'
                ]);

                // Riwayat Penerima
                Transaction::create([
                    'reference_id'   => $refBase . '-IN',
                    'user_id'        => $receiverId,
                    'merchant_id'    => $merchant?->id,
                    'type'           => 'receive',
                    'amount'         => $this->payAmount,
                    'description'    => $inDesc,
                    'latest_balance' => $receiverWallet->balance,
                    'status'         => 'success'
                ]);
            });
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
            return;
        }

        // Jalankan Queue Job untuk Notifikasi WhatsApp asinkron via API Fonnte
        $nominalFormatted = 'Rp ' . number_format((int) $this->payAmount, 0, ',', '.');

        if ($merchant) {
            SendWaNotification::dispatch($user->whatsapp, "Pembayaran {$nominalFormatted} ke Toko *{$merchant->merchant_name}* telah BERHASIL.");
            SendWaNotification::dispatch($merchant->user->whatsapp, "Toko *{$merchant->merchant_name}* menerima pembayaran masuk sebesar {$nominalFormatted} dari *{$user->name}*.");
        } else {
            $target = User::find($receiverId);
            SendWaNotification::dispatch($user->whatsapp, "Transfer {$nominalFormatted} ke *{$target->name}* telah BERHASIL.");
            SendWaNotification::dispatch($target->whatsapp, "Kamu menerima transfer {$nominalFormatted} dari *{$user->name}*.");
        }

        $this->lastRef = $refBase;
        $this->reset(['pin', 'note']);

        // Lanjut ke Step 2 (Layar Transaksi Sukses)
        $this->payStep = 2;
    }

    public function render()
    {
        // Pastikan Anda menggunakan layout kerangka utama
        return view('livewire.scan-qr');
    }
}
