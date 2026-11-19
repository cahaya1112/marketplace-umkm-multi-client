<?php

namespace App\Modules\Payments\Data;

final readonly class CreatePaymentRequest
{
    public function __construct(
        public string $orderId,
        public int $amount,
        public string $customerName,
        public string $customerEmail,
    ) {}
}