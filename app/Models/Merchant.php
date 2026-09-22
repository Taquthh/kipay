<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Merchant extends Model
{
    // Mengizinkan semua kolom diisi secara otomatis
    protected $guarded = [];

    // Relasi balik ke pemilik toko (User)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi toko ke banyak tagihan (Bills) - untuk persiapan fitur selanjutnya
    public function bills()
    {
        return $this->hasMany(Bill::class);
    }
}
