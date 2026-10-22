<?php
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return 'Halaman Katalog Publik Pembeli';
})->name('home');