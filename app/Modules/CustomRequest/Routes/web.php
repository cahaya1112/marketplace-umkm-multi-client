<?php

use Illuminate\Support\Facades\Route;
use App\Modules\CustomRequest\Controllers\CustomRequestController;

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/custom-request', [CustomRequestController::class, 'index'])
        ->name('custom-request.index');

    Route::post('/custom-request', [CustomRequestController::class, 'store'])
        ->middleware('role:customer')
        ->name('custom-request.store');
});

Route::middleware(['web', 'auth', 'role:umkm_owner'])->group(function () {
    Route::get('/custom-request/manage', [CustomRequestController::class, 'manage'])
        ->name('custom-request.manage');

    Route::put('/custom-request/{id}/respond', [CustomRequestController::class, 'respond'])
        ->name('custom-request.respond');
});
