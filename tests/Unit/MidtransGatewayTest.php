<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;

use App\Modules\Payments\Gateways\MidtransGateway;
use App\Modules\Payments\Data\CreatePaymentRequest;

class MidtransGatewayTest extends TestCase
{
    public function test_can_create_snap_transaction(): void
    {
        config([
            'services.midtrans.server_key' => 'SB-Mid-server-test',
            'services.midtrans.is_production' => false,
        ]);

        Http::fake([
            'https://app.sandbox.midtrans.com/snap/v1/transactions'
                => Http::response([
                    'token' => 'fake-snap-token',
                    'redirect_url' => 'https://example.com/payment',
                ], 201),
        ]);

        $gateway = new MidtransGateway();

        $result = $gateway->createPayment(
            new CreatePaymentRequest(
                orderId: 'ORDER-TEST-001',
                amount: 150000,
                customerName: 'Pembeli Test',
                customerEmail: 'test@example.com',
            )
        );

        $this->assertSame('PENDING', $result->status);
        $this->assertSame('fake-snap-token', $result->token);
        $this->assertSame(
            'https://example.com/payment',
            $result->redirectUrl
        );

        Http::assertSent(function (Request $request) {
            return $request->url() ===
                'https://app.sandbox.midtrans.com/snap/v1/transactions'
                && $request->method() === 'POST';
        });
    }

    public function test_handles_midtrans_api_error(): void
    {
        config([
            'services.midtrans.server_key' => 'SB-Mid-server-test',
            'services.midtrans.is_production' => false,
        ]);

        Http::fake([
            'https://app.sandbox.midtrans.com/snap/v1/transactions'
                => Http::response([
                    'error_messages' => [
                        'Invalid transaction details',
                    ],
                ], 400),
        ]);

        $gateway = new MidtransGateway();

        $this->expectException(RequestException::class);

        $gateway->createPayment(
            new CreatePaymentRequest(
                orderId: 'ORDER-ERROR-001',
                amount: 150000,
                customerName: 'Pembeli Test',
                customerEmail: 'test@example.com',
            )
        );
    }
}
