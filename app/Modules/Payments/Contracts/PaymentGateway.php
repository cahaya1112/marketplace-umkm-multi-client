<?php

namespace App\Modules\Payments\Contracts;

use App\Modules\Payments\Data\CreatePaymentRequest;
use App\Modules\Payments\Data\PaymentResult;

interface PaymentGateway
{
    public function createPayment(
        CreatePaymentRequest $request
    ): PaymentResult;
}