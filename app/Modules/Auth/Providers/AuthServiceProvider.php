<?php

namespace App\Modules\Auth\Providers;

use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register dependensi khusus modul Auth jika ada
    }

    public function boot(): void
    {
        // Mendaftarkan Route Khusus Modul Auth
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
    }
}