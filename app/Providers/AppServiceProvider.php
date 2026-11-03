<?php

namespace App\Providers;

use App\Modules\Umkm\Umkm;
use App\Policies\UmkmPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Model Umkm berada di App\Modules\Umkm, bukan App\Models,
        // sehingga policy harus didaftarkan manual (tidak ditemukan otomatis).
        Gate::policy(Umkm::class, UmkmPolicy::class);

        // Parameter {umkm} pada route otomatis menjadi model Umkm (kunci: id_umkm).
        // Jika id tidak ada, Laravel otomatis menjawab 404.
        Route::model('umkm', Umkm::class);
    }
}