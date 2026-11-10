<?php

use App\Modules\Ordering\Controllers\KeranjangController;
use Illuminate\Support\Facades\Route;
use App\Modules\Ordering\Controllers\CustomPesananController;

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/keranjang', [KeranjangController::class, 'index'])
        ->name('keranjang.index');

    Route::post('/keranjang/tambah/{id_produk}', [KeranjangController::class, 'store'])
        ->whereNumber('id_produk')
        ->name('keranjang.tambah');

    Route::patch('/keranjang/{id}', [KeranjangController::class, 'update'])
        ->whereNumber('id')
        ->name('keranjang.update');

    Route::delete('/keranjang/{id}', [KeranjangController::class, 'destroy'])
        ->whereNumber('id')
        ->name('keranjang.destroy');

    Route::post('/produk/{id_produk}/custom', [CustomPesananController::class, 'store']
    )->whereNumber('id_produk')->name('custom.store');

    Route::get('/custom-saya', [CustomPesananController::class, 'index'])
        ->name('custom.saya');

    Route::patch('/custom-saya/{id}/{aksi}', [CustomPesananController::class, 'keputusan'])
        ->whereNumber('id')
        ->whereIn('aksi', ['setujui', 'tolak'])
        ->name('custom.keputusan');

    Route::post('/custom-saya/{id}/keranjang', [CustomPesananController::class,'masukkanKeranjang'])
        ->whereNumber('id')
        ->name('custom.keranjang');

});
