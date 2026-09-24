<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'status'  => 'success',
        'message' => 'API Marketplace UMKM Service is running successfully.',
        'version' => '1.0.0',
    ]);
});