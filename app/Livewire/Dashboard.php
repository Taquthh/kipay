<?php

namespace App\Livewire;

use App\Models\Merchant;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Dashboard extends Component
{
    /** Tab aktif: beranda | riwayat */
    public string $tab = 'beranda';

    /** Panel */
    public bool $showTopUp   = false;
    public bool $showQr      = false;   // QR personal saya
    public bool $showScan    = false;   // sheet scan (kamera / manual)
    public bool $hideBalance = false;

    /** Top-up */
    public $amount;

    /** ===== Alur scan =====
     *  payStep 0 = belum scan, 1 = konfirmasi bayar/transfer, 2 = sukses
     */
    public int $payStep = 0;
    public string $payMode = '';        // 'merchant' | 'friend'
    public $payload;
    public $payAmount;
    public $pin;
    public $note;
    public ?array $payTarget = null;
    public $errorMessage = '';
    public $lastRef;
    public $targetMerchantId = null;
    public $targetUserId = null;

    /* =====================================================
     | Navigasi
     |=====================================================*/
    public function setTab(string $tab): void
    {
        $this->tab = in_array($tab, ['beranda', 'riwayat']) ? $tab : 'beranda';
    }

    public function openScan(): void
    {
        $this->resetScan();
        $this->showScan = true;
    }

    public function openTopUp(): void
    {
        $this->reset('amount');
        $this->resetValidation();
        $this->showTopUp = true;
    }

    public function resetScan(): void
    {
        $this->reset(['payload', 'payAmount', 'pin', 'note', 'payTarget', 'lastRef', 'targetMerchantId', 'targetUserId']);
        $this->payStep = 0;
        $this->payMode = '';
        $this->errorMessage = '';
        $this->resetValidation();
    }

    public function closeAll(): void
    {
        $this->showTopUp = $this->showQr = $this->showScan = false;
        $this->resetScan();
    }

    /* =====================================================
     | QR personal milik user (untuk menerima transfer teman)
     |=====================================================*/
    public function getMyPayloadProperty(): string
    {
        return 'KIPAY-USR-' . Auth::id();
    }

    /* =====================================================
     | STEP 1 — resolve payload (kamera atau input manual)
     |=====================================================*/
    public function scan(?string $raw = null): void
    {
        $this->payload = trim((string) ($raw ?? $this->payload));
        $this->errorMessage = '';
        $this->showScan = true;

        if ($this->payload === '') {
            $this->errorMessage = 'QR tidak terbaca, coba lagi.';

            return;
        }

        // --- QR Merchant: KIPAY-MCH-{userId}-{timestamp}
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
            $this->payStep = 1;

            return;
        }

        // --- QR Personal: KIPAY-USR-{id}
        if (str_starts_with($this->payload, 'KIPAY-USR-')) {
            $target = User::find((int) str_replace('KIPAY-USR-', '', $this->payload));

            if (! $target) {
                $this->errorMessage = 'Pengguna tidak ditemukan.';

                return;
            }

            if ($target->id === Auth::id()) {
                $this->errorMessage = 'Tidak dapat transfer ke akun sendiri.';

                return;
            }

            $this->payMode = 'friend';
            $this->targetUserId = $target->id;
            $this->payTarget = [
                'title'    => $target->name,
                'subtitle' => $target->whatsapp,
                'initial'  => strtoupper(mb_substr($target->name, 0, 1)),
            ];
            $this->payStep = 1;

            return;
        }

        $this->errorMessage = 'QR Code tidak dikenali oleh KiPay.';
    }

    /* =====================================================
     | STEP 2 — bayar / transfer (pessimistic locking)
     |=====================================================*/
    public function pay(): void
    {
        $this->validate([
            'payAmount' => 'required|numeric|min:1000',
            'pin'       => 'required|numeric|digits:6',
            'note'      => 'nullable|string|max:60',
        ], [], ['payAmount' => 'nominal']);

        $user = Auth::user();

        if (! $user->pin || ! Hash::check($this->pin, $user->pin)) {
            $this->errorMessage = 'PIN salah!';
            $this->reset('pin');

            return;
        }

        $this->errorMessage = '';
        $merchant   = $this->targetMerchantId ? Merchant::find($this->targetMerchantId) : null;
        $receiverId = $this->targetUserId;
        $refBase = ($this->payMode === 'merchant' ? 'PAY-' : 'TRF-') . now()->format('ymdHis') . '-' . $user->id . '-' . random_int(100, 999);
        $refOut  = $refBase . '-OUT';
        $refIn   = $refBase . '-IN';

        try {
            DB::transaction(function () use ($user, $merchant, $receiverId, $refOut, $refIn) {
                // Kunci baris dompet pengirim & penerima
                $senderWallet   = Wallet::where('user_id', $user->id)->lockForUpdate()->first();
                $receiverWallet = Wallet::where('user_id', $receiverId)->lockForUpdate()->first();

                if (! $receiverWallet) {
                    throw new \Exception('Dompet penerima tidak ditemukan.');
                }

                if ($senderWallet->balance < $this->payAmount) {
                    throw new \Exception('Saldo Anda tidak mencukupi.');
                }

                $senderWallet->balance -= $this->payAmount;
                $senderWallet->save();

                $receiverWallet->balance += $this->payAmount;
                $receiverWallet->save();

                $catatan = $this->note ? ' (' . $this->note . ')' : '';

                if ($merchant) {
                    $descOut = 'Pembayaran ke Merchant: ' . $merchant->merchant_name . $catatan;
                    $descIn  = 'Pembayaran masuk dari: ' . $user->name . $catatan;
                    $typeOut = 'payment';
                } else {
                    $descOut = 'Transfer ke ' . $this->payTarget['title'] . $catatan;
                    $descIn  = 'Transfer dari ' . $user->name . $catatan;
                    $typeOut = 'transfer';
                }

                Transaction::create([
                    'reference_id'   => $refOut,
                    'user_id'        => $user->id,
                    'merchant_id'    => $merchant?->id,
                    'type'           => $typeOut,
                    'amount'         => $this->payAmount,
                    'description'    => $descOut,
                    'latest_balance' => $senderWallet->balance,
                    'status'         => 'success',
                ]);

                Transaction::create([
                    'reference_id'   => $refIn,
                    'user_id'        => $receiverId,
                    'merchant_id'    => $merchant?->id,
                    'type'           => 'receive',
                    'amount'         => $this->payAmount,
                    'description'    => $descIn,
                    'latest_balance' => $receiverWallet->balance,
                    'status'         => 'success',
                ]);
            });
        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();

            return;
        }

        // Notifikasi WhatsApp (queue)
        $nominal = 'Rp ' . number_format($this->payAmount, 0, ',', '.');

        if ($merchant) {
            \App\Jobs\SendWaNotification::dispatch(
                $user->whatsapp,
                "Pembayaran {$nominal} ke Merchant *{$merchant->merchant_name}* BERHASIL."
            );
            \App\Jobs\SendWaNotification::dispatch(
                $merchant->user->whatsapp,
                "Toko *{$merchant->merchant_name}* menerima pembayaran masuk sebesar {$nominal} dari *{$user->name}*."
            );
        } else {
            $target = User::find($receiverId);
            \App\Jobs\SendWaNotification::dispatch(
                $user->whatsapp,
                "Transfer {$nominal} ke *{$target->name}* BERHASIL."
            );
            \App\Jobs\SendWaNotification::dispatch(
                $target->whatsapp,
                "Kamu menerima transfer {$nominal} dari *{$user->name}*."
            );
        }

        $this->lastRef = $refBase;
        $this->reset(['pin', 'note']);
        $this->payStep = 2;
    }

    /* =====================================================
     | Top-up dummy
     |=====================================================*/
    public function topUp(): void
    {
        $this->validate(['amount' => 'required|numeric|min:10000|max:10000000']);

        $user = Auth::user();

        DB::transaction(function () use ($user) {
            $wallet = $user->wallet()->lockForUpdate()->first();
            $wallet->balance += $this->amount;
            $wallet->save();

            Transaction::create([
                'reference_id'   => 'TOPUP-' . now()->format('ymdHis') . '-' . $user->id,
                'user_id'        => $user->id,
                'type'           => 'topup',
                'amount'         => $this->amount,
                'description'    => 'Top-Up Saldo (Dummy)',
                'latest_balance' => $wallet->balance,
                'status'         => 'success',
            ]);
        });

        $this->reset('amount');
        $this->showTopUp = false;
        session()->flash('success', 'Top-Up berhasil, saldo kamu sudah bertambah.');
    }

    public function logout()
    {
        Auth::logout();

        return redirect()->route('login');
    }

    public function render()
    {
        $user = Auth::user();
        $query = $user->transactions()->latest();

        return view('livewire.dashboard', [
            'user'                => $user,
            'wallet'              => $user->wallet,
            'merchant'            => $user->merchant,
            'recent_transactions' => (clone $query)->take(5)->get(),
            'all_transactions'    => $this->tab === 'riwayat' ? $query->take(50)->get() : collect(),
        ]);
    }
}
