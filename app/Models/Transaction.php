<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    // Mengizinkan semua kolom di tabel transactions diisi secara otomatis
    protected $guarded = [];

    // Relasi balik ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
