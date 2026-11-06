<?php

use App\Modules\Catalog\Controllers\CatalogController;
use Illuminate\Support\Facades\Route;

// ModuleServiceProvider memuat file ini lewat loadRoutesFrom() tanpa grup middleware,
// jadi 'web' dipasang di sini agar session, CSRF, dan @auth berfungsi.
Route::middleware('web')->group(function () {
    Route::get('/katalog', [CatalogController::class, 'index'])->name('katalog.index');
    Route::get('/produk/{id}', [CatalogController::class, 'show'])->whereNumber('id')->name('produk.show');
});