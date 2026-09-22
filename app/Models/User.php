<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'whatsapp', 'pin', 'otp_code', 'otp_expires_at'])]
#[Hidden(['pin', 'otp_code'])]
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            // Mengubah tipe kolom waktu agar otomatis menjadi instance Carbon
            'otp_expires_at' => 'datetime',
            // Memastikan PIN selalu diperlakukan sebagai hash
            'pin' => 'hashed',
        ];
    }

    // ==========================================
    // ELOQUENT RELATIONSHIPS
    // ==========================================

    // Relasi: User memiliki 1 Dompet
    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }

    // Relasi: User memiliki 1 Merchant (Toko) - Opsional
    public function merchant()
    {
        return $this->hasOne(Merchant::class);
    }

    // Relasi: User memiliki banyak Riwayat Transaksi
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
