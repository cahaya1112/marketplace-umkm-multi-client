<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Umkm\Controllers\UmkmController;

Route::prefix('api/umkm')->group(function () {
    Route::get('/', [UmkmController::class, 'index']);
    Route::get('/{id}', [UmkmController::class, 'show']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/', [UmkmController::class, 'store']);
        Route::put('/{id}', [UmkmController::class, 'update']);
    });
});