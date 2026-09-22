<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Merchant;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class MerchantProfile extends Component
{
    public $merchant_name;
    public $hasMerchant = false;
    public $merchantData;

    public function mount()
    {
        $this->checkMerchant();
    }

    public function checkMerchant()
    {
        $user = Auth::user();
        if ($user->merchant) {
            $this->hasMerchant = true;
            $this->merchantData = $user->merchant;
        }
    }

    public function createMerchant()
    {
        $this->validate([
            'merchant_name' => 'required|string|min:3|max:50'
        ]);

        $user = Auth::user();

        // Buat payload QR Unik (Contoh: KIPAY-MCH-1-TIMESTAMP)
        $uniquePayload = 'KIPAY-MCH-' . $user->id . '-' . time();

        Merchant::create([
            'user_id' => $user->id,
            'merchant_name' => $this->merchant_name,
            'qr_code_payload' => $uniquePayload
        ]);

        // Refresh data
        $this->checkMerchant();
    }

    public function render()
    {
        $qrCodeSvg = null;

        if ($this->hasMerchant) {
            // Generate QR Code format SVG
            $qrCodeSvg = QrCode::size(250)
                ->margin(2)
                ->generate($this->merchantData->qr_code_payload);
        }

        return view('livewire.merchant-profile', [
            'qrCodeSvg' => $qrCodeSvg
        ]);
    }
}
