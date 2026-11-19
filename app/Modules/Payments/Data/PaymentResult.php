<?php

namespace App\Modules\Payments\Data;

final readonly class PaymentResult
{
    public function __construct(
        public ?string $transactionId,
        public string $status,
        public ?string $redirectUrl = null,
        public ?string $token = null,
    ) {}
}