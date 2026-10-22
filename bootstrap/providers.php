<?php

use App\Providers\AppServiceProvider;
use App\Providers\FortifyServiceProvider;

return [
    App\Providers\AppServiceProvider::class,
    FortifyServiceProvider::class,
    // Modul Marketplace
    App\Modules\Admin\AdminServiceProvider::class,
    App\Modules\Umkm\UmkmServiceProvider::class,
    App\Modules\Catalog\CatalogServiceProvider::class,
    App\Modules\Ordering\OrderingServiceProvider::class,
    App\Modules\Payments\PaymentsServiceProvider::class,
    App\Modules\Api\ApiServiceProvider::class,
];
