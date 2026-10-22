<?php

use App\Http\Controllers\DashboardRedirectController;
use Illuminate\Support\Facades\Route;

// Rute ini akan dipanggil otomatis setelah user berhasil login
Route::get('dashboard', DashboardRedirectController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Pertahankan rute settings bawaan dari starter kit Anda
require __DIR__.'/settings.php';