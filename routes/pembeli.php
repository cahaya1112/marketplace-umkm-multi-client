<?php

use App\Http\Controllers\KatalogPublikController;
use Illuminate\Support\Facades\Route;

Route::get('/', KatalogPublikController::class)->name('home');