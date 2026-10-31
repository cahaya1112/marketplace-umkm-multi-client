<?php

use Illuminate\Support\Facades\Route;

Route::get('/dashboard', function (string $umkm) {
    return 'Dashboard UMKM: ' . $umkm;
})->name('dashboard');