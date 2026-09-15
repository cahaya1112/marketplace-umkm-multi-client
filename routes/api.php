<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/product', function () {
    return response()->json([
        'success' => true,
        'message' => 'Data produk berhasil diambil',
        'data' => []
    ]);
});

Route::post('/product', function (Request $request) {
    return response()->json([
        'success' => true,
        'message' => 'Data produk berhasil ditambahkan',
        'data' => $request->all()
    ], 201);
});

Route::put('/product/{id}', function (Request $request, $id) {
    return response()->json([
        'success' => true,
        'message' => 'Data produk berhasil diperbarui',
        'data' => [
            'id' => $id,
            ...$request->all()
        ]
    ]);
});