<?php

namespace App\Services\PaymentGateways;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MollieGateway implements PaymentGatewayInterface
{
    /**
     * Creates a payment and returns Mollie's hosted checkout URL for the browser to visit.
     */
    public function createPayment(Order $order, array $paymentData): array
    {
        try {
            $payload = [
                'amount' => [
                    'currency' => strtoupper($order->currency),
                    'value' => number_format((float) $order->total, 2, '.', ''),
                ],
                'description' => __('emails.order_confirmation_subject', ['order_id' => $order->id]),
                'redirectUrl' => route('checkout.success', $order->id),
                'metadata' => ['order_id' => (string) $order->id],
            ];

            // Mollie rejects webhook URLs it cannot reach, so skip it for local development
            if (! app()->environment('local')) {
                $payload['webhookUrl'] = route('webhooks.mollie');
            }

            $response = $this->client()->post('/payments', $payload);

            if ($response->successful() && $response->json('_links.checkout.href')) {
                return [
                    'success' => true,
                    'transaction_id' => $response->json('id'),
                    'hosted_url' => $response->json('_links.checkout.href'),
                    'response' => $response->json(),
                ];
            }

            Log::error('Mollie payment creation failed', [
                'order_id' => $order->id,
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            return ['success' => false, 'error' => 'Payment initialization failed', 'response' => $response->json()];
        } catch (\Exception $e) {
            Log::error('Mollie payment creation failed', ['order_id' => $order->id, 'error' => $e->getMessage()]);

            return ['success' => false, 'error' => 'Payment initialization failed'];
        }
    }

    /**
     * Mollie webhooks are unsigned, so the payment is always re-fetched with our API key.
     */
    public function retrievePayment(string $transactionId): array
    {
        $response = $this->client()->get("/payments/{$transactionId}");

        return [
            'success' => $response->successful(),
            'status' => $response->json('status'),
            'metadata' => $response->json('metadata', []),
            'response' => $response->json(),
        ];
    }

    public function capturePayment(string $transactionId): array
    {
        return ['success' => false, 'error' => 'Mollie payments are captured at checkout'];
    }

    public function refundPayment(string $transactionId, float $amount): array
    {
        $payment = $this->retrievePayment($transactionId);

        $response = $this->client()->post("/payments/{$transactionId}/refunds", [
            'amount' => [
                'currency' => $payment['response']['amount']['currency'] ?? null,
                'value' => number_format($amount, 2, '.', ''),
            ],
        ]);

        return ['success' => $response->successful(), 'response' => $response->json()];
    }

    public function getClientToken(): ?string
    {
        return null;
    }

    /**
     * Mollie sends only a form-encoded payment id, which is trusted once
     * retrievePayment() confirms it exists on our account.
     */
    public function verifyWebhook(Request $request): bool
    {
        return str_starts_with((string) $request->input('id'), 'tr_');
    }

    private function client()
    {
        return Http::withToken(config('payment.gateways.mollie.api_key'))
            ->baseUrl('https://api.mollie.com/v2')
            ->acceptJson();
    }
}
