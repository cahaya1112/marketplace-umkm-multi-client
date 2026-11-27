
<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Ordering\Controllers\KeranjangController;
use App\Modules\Ordering\Controllers\CustomPesananController;
use App\Modules\Ordering\Controllers\CheckoutController;
use App\Modules\Ordering\Controllers\AlamatPembeliController;
use App\Modules\Ordering\Controllers\RiwayatPesananController;
use App\Modules\Ordering\Controllers\BeliSekarangController;
use App\Modules\Ordering\Controllers\PesananUmkmController;

Route::middleware(['web', 'auth'])->group(function () {

    // Keranjang
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


    // Pesanan Custom
    Route::post('/produk/{id_produk}/custom', [CustomPesananController::class, 'store'])
        ->whereNumber('id_produk')
        ->name('custom.store');

    Route::get('/custom-saya', [CustomPesananController::class, 'index'])
        ->name('custom.saya');

    Route::patch('/custom-saya/{id}/{aksi}', [CustomPesananController::class, 'keputusan'])
        ->whereNumber('id')
        ->whereIn('aksi', ['setujui', 'tolak'])
        ->name('custom.keputusan');

    Route::post('/custom-saya/{id}/keranjang', [CustomPesananController::class, 'masukkanKeranjang'])
        ->whereNumber('id')
        ->name('custom.keranjang');


    // Checkout
    Route::get('/checkout', [CheckoutController::class, 'index'])
        ->name('checkout.index');

    Route::post('/checkout', [CheckoutController::class, 'store'])
        ->name('checkout.store');

    Route::post('/checkout/ongkir', [CheckoutController::class, 'hitungOngkir'])
        ->name('checkout.ongkir');

    Route::post('/produk/{id}/beli-sekarang', [BeliSekarangController::class, 'store'])
        ->whereNumber('id')
        ->name('checkout.langsung');


    // Kelola Alamat Pembeli
    Route::get('/alamat', [AlamatPembeliController::class, 'index'])
        ->name('alamat.index');

    Route::get('/alamat/tambah', [AlamatPembeliController::class, 'create'])
        ->name('alamat.create');

    Route::post('/alamat', [AlamatPembeliController::class, 'store'])
        ->name('alamat.store');

    Route::get('/alamat/{id}/edit', [AlamatPembeliController::class, 'edit'])
        ->whereNumber('id')
        ->name('alamat.edit');

    Route::put('/alamat/{id}', [AlamatPembeliController::class, 'update'])
        ->whereNumber('id')
        ->name('alamat.update');

    Route::delete('/alamat/{id}', [AlamatPembeliController::class, 'destroy'])
        ->whereNumber('id')
        ->name('alamat.destroy');

    Route::get('/api/alamat/wilayah', [AlamatPembeliController::class, 'cariWilayah'])
        ->name('alamat.wilayah');


    // Riwayat dan Pelacakan Pesanan Pembeli
    Route::get('/pesanan-saya', [RiwayatPesananController::class, 'index'])
        ->name('pesanan.saya');

    Route::get('/pesanan-saya/{id}', [RiwayatPesananController::class, 'detail'])
        ->whereNumber('id')
        ->name('pesanan.detail');


    // Pengelolaan Pesanan UMKM
    Route::get('/umkm/pesanan', [PesananUmkmController::class, 'index'])
        ->name('kelola.pesanan.index');

    Route::get('/umkm/pesanan/{id}', [PesananUmkmController::class, 'show'])
        ->whereNumber('id')
        ->name('kelola.pesanan.show');

    Route::patch('/umkm/pesanan/{id}/status', [PesananUmkmController::class, 'updateStatus'])
        ->whereNumber('id')
        ->name('kelola.pesanan.update-status');

});
