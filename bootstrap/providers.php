<?php

use App\Providers\AppServiceProvider;
use App\Providers\FortifyServiceProvider;

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\FortifyServiceProvider::class,
    App\Modules\Auth\Providers\AuthServiceProvider::class,
    App\Modules\Umkm\Providers\UmkmServiceProvider::class,
    App\Modules\Catalog\Providers\CatalogServiceProvider::class,
    App\Modules\CustomRequest\Providers\CustomRequestServiceProvider::class,
    App\Modules\Cart\Providers\CartServiceProvider::class,
    App\Modules\Payment\Providers\PaymentServiceProvider::class,
];
