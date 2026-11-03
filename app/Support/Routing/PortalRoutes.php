<?php
namespace App\Support\Routing;

use Closure;
use Illuminate\Support\Facades\Route;

final class PortalRoutes
{
    public static function pembeli(Closure|string $routes): void
    {
        // Pembeli publik, beberapa fitur butuh auth (diatur per route nanti)
        Route::middleware(['web'])
            ->name('pembeli.')
            ->group($routes);
    }

    public static function umkm(Closure|string $routes): void
    {
        Route::middleware(['web', 'auth', 'role:ADMIN_UMKM', 'umkm.access'])
            ->prefix('umkm/{umkm}')
            ->name('umkm.')
            ->group($routes);
    }

    public static function admin(Closure|string $routes): void
    {
        Route::middleware(['web', 'auth', 'role:SUPER_ADMIN'])
            ->prefix('admin')
            ->name('admin.')
            ->group($routes);
    }
}