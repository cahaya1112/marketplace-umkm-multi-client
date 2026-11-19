<?php
namespace App\Modules\Payments;

use App\Modules\ModuleServiceProvider;

final class PaymentsServiceProvider extends ModuleServiceProvider
{
    public function register(): void
{
    $this->app->bind(
        \App\Modules\Payments\Contracts\PaymentGateway::class,
        \App\Modules\Payments\Gateways\MidtransGateway::class
    );
}

    protected function moduleAlias(): string
    {
        return 'payments';
    }
}