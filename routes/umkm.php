<?php

use App\Modules\Umkm\Umkm;
use Illuminate\Support\Facades\Route;

// Semua route di file ini sudah berada di bawah prefix umkm/{umkm}
// dan middleware: auth, role:ADMIN_UMKM, umkm.access (lihat PortalRoutes).
Route::get('/dashboard', function (Umkm $umkm) {
    return 'Dashboard UMKM: '.$umkm->nama;
})->name('dashboard');