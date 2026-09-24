<?php

namespace App\Modules\Cart\Providers;

use Illuminate\Support\ServiceProvider;

class CartServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');
        $this->loadRoutesFrom(__DIR__.'/../Routes/api.php');
    }
}
