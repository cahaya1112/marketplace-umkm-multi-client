<?php

namespace App\Modules\Ordering\Providers;

use Illuminate\Support\ServiceProvider;

class OrderingServiceProvider extends ServiceProvider
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
