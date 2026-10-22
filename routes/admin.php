<?php
use Illuminate\Support\Facades\Route;

Route::get('/dashboard', function () {
    return 'Dashboard Super Admin';
})->name('dashboard');