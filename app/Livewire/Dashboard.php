<?php

namespace App\Livewire;

use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Dashboard extends Component
{
    public string $tab = 'beranda';
    public bool $showTopUp = false;
    public bool $hideBalance = false;
    public bool $showSuccessModal = false;
    public string $successMessage = '';
    public $amount;

    // batas atas top-up dummy: 999.999.999.999 (hampir 1 triliun)
    public const MAX_TOPUP = 999999999999;

    public function setTab(string $tab): void
    {
        $this->tab = in_array($tab, ['beranda', 'riwayat']) ? $tab : 'beranda';
    }

    public function openTopUp(): void
    {
        $this->reset('amount');
        $this->resetValidation();
        $this->showTopUp = true;
    }

    public function setQuickAmount($value): void
    {
        $this->amount = $value;
    }

    public function closeAll(): void
    {
        $this->showTopUp = false;
    }

    public function closeSuccessModal(): void
    {
        $this->showSuccessModal = false;
        $this->successMessage = '';
    }

    public function topUp(): void
    {
        $this->validate([
            'amount' => 'required|numeric|min:10000|max:' . self::MAX_TOPUP,
        ]);

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

        $nominal = 'Rp ' . number_format($this->amount, 0, ',', '.');
        $this->reset('amount');
        $this->showTopUp = false;

        $this->successMessage = "Top-Up berhasil, saldo kamu bertambah {$nominal}.";
        $this->showSuccessModal = true;
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
