<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\LoginRegister;
use App\Livewire\Dashboard;
use App\Livewire\MerchantProfile;
use App\Livewire\Transfer;

Route::get('/transfer', Transfer::class)->middleware('auth')->name('transfer');

Route::get('/merchant', MerchantProfile::class)->middleware('auth')->name('merchant');

// Rute Login & Register menggunakan Livewire Component
Route::get('/', LoginRegister::class)->name('login');

// Rute sementara untuk Dashboard agar tidak error setelah login
Route::get('/dashboard', Dashboard::class)->middleware('auth')->name('dashboard');
