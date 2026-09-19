<?php

namespace App\Modules\Umkm\Providers;

use Illuminate\Support\ServiceProvider;

class UmkmServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Mendaftarkan Route Khusus Modul UMKM
        $this->loadRoutesFrom(__DIR__ . '/../Routes/web.php');
        $this->loadRoutesFrom(__DIR__.'/../Routes/api.php');
    }
}