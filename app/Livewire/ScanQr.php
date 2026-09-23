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
use Throwable;

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

    #[On('scan')]
    public function scan(?string $raw = null): void
    {
        $this->payload = trim((string) ($raw ?? $this->payload));
        $this->errorMessage = '';

        if ($this->payload === '') {
            $this->errorMessage = 'QR tidak terbaca. Coba lagi atau pilih foto.';
            return;
        }

        if (str_starts_with($this->payload, 'KIPAY-MCH-')) {
            $merchant = Merchant::where('qr_code_payload', $this->payload)->first();
            if (! $merchant) {
                $this->errorMessage = 'QR Code tidak valid atau merchant tidak ditemukan.';
                return;
            }
            if ((int) $merchant->user_id === (int) Auth::id()) {
                $this->errorMessage = 'Tidak dapat melakukan pembayaran ke toko sendiri.';
                return;
            }

            $this->payMode = 'merchant';
            $this->targetMerchantId = $merchant->id;
            $this->targetUserId = $merchant->user_id;
            $this->payTarget = [
                'title' => $merchant->merchant_name,
                'subtitle' => 'Merchant KiPay',
                'initial' => strtoupper(mb_substr($merchant->merchant_name, 0, 1)),
            ];
            $this->payStep = 1;
            return;
        }

        if (str_starts_with($this->payload, 'KIPAY-USR-')) {
            $userId = (int) str_replace('KIPAY-USR-', '', $this->payload);
            $target = User::find($userId);
            if (! $target) {
                $this->errorMessage = 'Pengguna tidak ditemukan.';
                return;
            }
            if ((int) $target->id === (int) Auth::id()) {
                $this->errorMessage = 'Tidak dapat transfer ke akun sendiri.';
                return;
            }

            $this->payMode = 'friend';
            $this->targetUserId = $target->id;
            $this->payTarget = [
                'title' => $target->name,
                'subtitle' => $target->whatsapp,
                'initial' => strtoupper(mb_substr($target->name, 0, 1)),
            ];
            $this->payStep = 1;
            return;
        }

        $this->errorMessage = 'QR Code tidak dikenali oleh KiPay.';
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
        $this->dispatch('qris-reset');
    }

    public function pay(): void
    {
        $this->validate([
            'payAmount' => ['required', 'numeric', 'integer', 'min:1000'],
            'pin' => ['required', 'digits:6'],
            'note' => ['nullable', 'string', 'max:60'],
        ], [], ['payAmount' => 'nominal']);

        $user = Auth::user();
        if (! $user->pin || ! Hash::check($this->pin, $user->pin)) {
            $this->errorMessage = 'PIN salah.';
            $this->reset('pin');
            return;
        }

        $merchant = $this->targetMerchantId ? Merchant::find($this->targetMerchantId) : null;
        $receiverId = $this->targetUserId;
        $refBase = ($this->payMode === 'merchant' ? 'PAY-' : 'TRF-') . now()->format('ymdHis') . '-' . $user->id . '-' . random_int(100, 999);

        try {
            DB::transaction(function () use ($user, $merchant, $receiverId, $refBase): void {
                $senderWallet = Wallet::where('user_id', $user->id)->lockForUpdate()->first();
                $receiverWallet = Wallet::where('user_id', $receiverId)->lockForUpdate()->first();
                if (! $senderWallet || ! $receiverWallet) throw new \RuntimeException('Dompet tidak ditemukan.');
                if ($senderWallet->balance < $this->payAmount) throw new \RuntimeException('Saldo Anda tidak mencukupi.');

                $senderWallet->decrement('balance', $this->payAmount);
                $receiverWallet->increment('balance', $this->payAmount);
                $senderWallet->refresh();
                $receiverWallet->refresh();
                $suffix = $this->note ? ' (' . $this->note . ')' : '';
                $outDescription = $merchant ? 'Pembayaran ke Merchant: ' . $merchant->merchant_name . $suffix : 'Transfer ke ' . $this->payTarget['title'] . $suffix;
                $inDescription = $merchant ? 'Pembayaran masuk dari: ' . $user->name . $suffix : 'Transfer dari ' . $user->name . $suffix;

                Transaction::create(['reference_id' => $refBase . '-OUT', 'user_id' => $user->id, 'merchant_id' => $merchant?->id, 'type' => $merchant ? 'payment' : 'transfer', 'amount' => $this->payAmount, 'description' => $outDescription, 'latest_balance' => $senderWallet->balance, 'status' => 'success']);
                Transaction::create(['reference_id' => $refBase . '-IN', 'user_id' => $receiverId, 'merchant_id' => $merchant?->id, 'type' => 'receive', 'amount' => $this->payAmount, 'description' => $inDescription, 'latest_balance' => $receiverWallet->balance, 'status' => 'success']);
            });
        } catch (Throwable $exception) {
            $this->errorMessage = $exception->getMessage();
            return;
        }

        $nominal = 'Rp ' . number_format((int) $this->payAmount, 0, ',', '.');
        SendWaNotification::dispatch($user->whatsapp, "Transaksi {$nominal} berhasil.");
        $this->lastRef = $refBase;
        $this->reset(['pin', 'note']);
        $this->payStep = 2;
    }

    public function render()
    {
        return view('livewire.scan-qr');
    }
}
