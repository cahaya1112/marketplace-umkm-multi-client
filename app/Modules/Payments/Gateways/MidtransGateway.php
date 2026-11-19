<?php

namespace App\Modules\Payments\Gateways;

use App\Modules\Payments\Contracts\PaymentGateway;
use App\Modules\Payments\Data\CreatePaymentRequest;
use App\Modules\Payments\Data\PaymentResult;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class MidtransGateway implements PaymentGateway
{
    public function createPayment(
        CreatePaymentRequest $request
    ): PaymentResult {
        $serverKey = config('services.midtrans.server_key');

        if (empty($serverKey)) {
            throw new RuntimeException(
                'Midtrans Server Key belum dikonfigurasi.'
            );
        }

        $isProduction = config(
            'services.midtrans.is_production',
            false
        );

        $baseUrl = $isProduction
            ? 'https://app.midtrans.com'
            : 'https://app.sandbox.midtrans.com';

        $response = Http::withBasicAuth($serverKey, '')
            ->acceptJson()
            ->post($baseUrl . '/snap/v1/transactions', [
                'transaction_details' => [
                    'order_id' => $request->orderId,
                    'gross_amount' => $request->amount,
                ],
                'customer_details' => [
                    'first_name' => $request->customerName,
                    'email' => $request->customerEmail,
                ],
            ]);

        $response->throw();

        $data = $response->json();

        return new PaymentResult(
            transactionId: null,
            status: 'PENDING',
            redirectUrl: $data['redirect_url'] ?? null,
            token: $data['token'] ?? null,
        );
    }
}