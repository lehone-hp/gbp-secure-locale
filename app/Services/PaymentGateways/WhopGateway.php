<?php

namespace App\Services\PaymentGateways;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhopGateway implements PaymentGatewayInterface
{
    private const WEBHOOK_TOLERANCE_SECONDS = 300;

    /**
     * Creates a checkout configuration with an inline one-time plan for the order.
     * The browser mounts it with Whop's embedded Checkout element.
     */
    public function createPayment(Order $order, array $paymentData): array
    {
        try {
            $response = $this->client()->post('/checkout_configurations', [
                'plan' => [
                    'company_id' => config('payment.gateways.whop.company_id'),
                    'currency' => strtolower($order->currency),
                    'initial_price' => (float) $order->total,
                    'plan_type' => 'one_time',
                ],
                'metadata' => ['order_id' => (string) $order->id],
                'redirect_url' => route('checkout.success', $order->id),
            ]);

            if ($response->successful() && $response->json('id')) {
                return [
                    'success' => true,
                    'transaction_id' => null,
                    'checkout_configuration_id' => $response->json('id'),
                    'response' => $response->json(),
                ];
            }

            Log::error('Whop checkout configuration failed', [
                'order_id' => $order->id,
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            return ['success' => false, 'error' => 'Payment initialization failed', 'response' => $response->json()];
        } catch (\Exception $e) {
            Log::error('Whop payment creation failed', ['order_id' => $order->id, 'error' => $e->getMessage()]);

            return ['success' => false, 'error' => 'Payment initialization failed'];
        }
    }

    public function capturePayment(string $transactionId): array
    {
        return ['success' => false, 'error' => 'Whop payments are captured at checkout'];
    }

    public function refundPayment(string $transactionId, float $amount): array
    {
        $response = $this->client()->post("/payments/{$transactionId}/refund", ['partial_amount' => $amount]);

        return ['success' => $response->successful(), 'response' => $response->json()];
    }

    public function getClientToken(): ?string
    {
        return null;
    }

    /**
     * Standard Webhooks signature: HMAC-SHA256 of "{id}.{timestamp}.{raw body}" with the
     * secret used as-is, compared against each "v1,<base64>" entry in the header.
     */
    public function verifyWebhook(Request $request): bool
    {
        $secret = config('payment.gateways.whop.webhook_secret');
        $id = $request->header('webhook-id');
        $timestamp = $request->header('webhook-timestamp');
        $signatures = $request->header('webhook-signature');

        if (!$secret || !$id || !$timestamp || !$signatures
            || abs(time() - (int) $timestamp) > self::WEBHOOK_TOLERANCE_SECONDS) {
            return false;
        }

        $expected = 'v1,' . base64_encode(hash_hmac('sha256', "{$id}.{$timestamp}.{$request->getContent()}", $secret, true));

        foreach (explode(' ', $signatures) as $signature) {
            if (hash_equals($expected, $signature)) {
                return true;
            }
        }

        return false;
    }

    private function client()
    {
        $base = config('payment.gateways.whop.environment') === 'production'
            ? 'https://api.whop.com/api/v1'
            : 'https://sandbox-api.whop.com/api/v1';

        return Http::withToken(config('payment.gateways.whop.api_key'))->baseUrl($base)->acceptJson();
    }
}
