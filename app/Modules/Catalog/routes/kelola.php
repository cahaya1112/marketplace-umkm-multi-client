<?php

use App\Modules\Catalog\Controllers\CustomController;
use App\Modules\Catalog\Controllers\ProdukController;
use App\Modules\Catalog\Controllers\StokController;
use App\Modules\Catalog\Controllers\TokoController;
use Illuminate\Support\Facades\Route;

// Dashboard Admin UMKM. Pembatasan peran dan kepemilikan UMKM dilakukan di KelolaController.
Route::middleware(['web', 'auth'])->prefix('kelola')->name('kelola.')->group(function () {
    Route::resource('produk', ProdukController::class)
        ->except(['show'])
        ->parameters(['produk' => 'id']);

    Route::get('stok', [StokController::class, 'index'])->name('stok.index');
    Route::put('stok/{id}', [StokController::class, 'update'])->whereNumber('id')->name('stok.update');

    Route::get('toko', [TokoController::class, 'edit'])->name('toko.edit');
    Route::put('toko', [TokoController::class, 'update'])->name('toko.update');

    Route::get('custom', [CustomController::class, 'index'])->name('custom.index');
    Route::post('custom/{id}/{aksi}', [CustomController::class, 'proses'])
        ->whereNumber('id')
        ->whereIn('aksi', ['setujui', 'tolak'])
        ->name('custom.proses');
});