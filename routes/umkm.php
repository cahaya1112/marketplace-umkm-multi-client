<?php

use App\Http\Controllers\UmkmDashboardController;
use Illuminate\Support\Facades\Route;

// Semua route di file ini sudah berada di bawah prefix umkm/{umkm}
// dan middleware: auth, role:ADMIN_UMKM, umkm.access (lihat PortalRoutes).
Route::get('/dashboard', UmkmDashboardController::class)->name('dashboard');