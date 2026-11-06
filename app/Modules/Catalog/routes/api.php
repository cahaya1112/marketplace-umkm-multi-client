<?php

use App\Modules\Catalog\Controllers\CatalogApiController;
use Illuminate\Support\Facades\Route;

// API katalog (FR-API-01) untuk aplikasi Flutter. Saat ini publik (hanya baca).
Route::middleware('api')->prefix('api/catalog')->name('api.catalog.')->group(function () {
    Route::get('produk', [CatalogApiController::class, 'produk'])->name('produk');
    Route::get('produk/{id}', [CatalogApiController::class, 'detailProduk'])->whereNumber('id')->name('produk.detail');
    Route::get('kategori', [CatalogApiController::class, 'kategori'])->name('kategori');
    Route::get('umkm/{id}', [CatalogApiController::class, 'umkm'])->whereNumber('id')->name('umkm');
});