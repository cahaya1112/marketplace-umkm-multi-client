<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Catalog\Controllers\CatalogController;

Route::get('/katalog', [CatalogController::class, 'index'])
    ->name('catalog.index');

Route::get('/katalog/manage', [CatalogController::class, 'manage'])
    ->middleware(['auth', 'role:umkm_owner'])
    ->name('catalog.manage');