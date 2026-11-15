<?php

namespace App\Services\PaymentGateways;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Order;
use Illuminate\Http\Request;
use SevenGps\PayUnit;
use Illuminate\Support\Facades\Log;

class PayUnitGateway implements PaymentGatewayInterface
{
    protected string $apiKey;
    protected string $apiUsername;
    protected string $apiPassword;
    protected string $mode;

    public function __construct()
    {
        $this->apiKey = config('payment.gateways.payunit.api_key');
        $this->apiUsername = config('payment.gateways.payunit.api_username');
        $this->apiPassword = config('payment.gateways.payunit.api_password');
        $this->mode = config('payment.gateways.payunit.mode', 'sandbox');
    }

    public function createPayment(Order $order, array $paymentData): array
    {
        try {
            // Initialize PayUnit SDK
            $payunit = new PayUnit(
                $this->apiKey,
                $this->apiPassword,
                $this->apiUsername,
                route('checkout.success', $order->id), // return_url
                route('webhooks.payunit'), // notify_url
                $this->mode,
                "Order #{$order->id}", // description
                $order->id, // purchaseRef
                $order->currency, // currency
                $order->customer_first_name . ' ' . $order->customer_last_name, // name
                'PU-' . $order->id . '-' . time() // transactionId
            );

            // Make payment request
            $response = $payunit->makePayment($order->total);

            Log::info('PayUnit payment initialization', [
                'order_id' => $order->id,
                'response' => $response
            ]);

            // Check if response is successful
            if (isset($response['status']) && $response['status'] === 'SUCCESS') {
                return [
                    'success' => true,
                    'transaction_id' => $response['data']['transaction_id'] ?? null,
                    'hosted_url' => $response['data']['transaction_url'] ?? null,
                    'response' => $response
                ];
            }

            return [
                'success' => false,
                'error' => $response['message'] ?? 'Payment initialization failed',
                'response' => $response
            ];

        } catch (\Exception $e) {
            Log::error('PayUnit payment creation failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => 'Payment initialization failed: ' . $e->getMessage()
            ];
        }
    }

    public function capturePayment(string $transactionId): array
    {
        // PayUnit auto-captures payments
        // This method is called when customer returns from hosted page
        // Payment confirmation happens via webhook

        Log::info('PayUnit capture payment called', ['transaction_id' => $transactionId]);

        return [
            'success' => true,
            'transaction_id' => $transactionId,
            'message' => 'Payment already captured by PayUnit'
        ];
    }

    public function refundPayment(string $transactionId, float $amount): array
    {
        // TODO: Implement refund API if available in PayUnit
        // Currently, refunds must be processed manually through PayUnit dashboard

        Log::warning('PayUnit refund requested but not implemented', [
            'transaction_id' => $transactionId,
            'amount' => $amount
        ]);

        return [
            'success' => false,
            'error' => 'Refunds must be processed manually through PayUnit dashboard'
        ];
    }

    public function getClientToken(): ?string
    {
        // PayUnit uses hosted page, no client token needed
        return null;
    }

    public function verifyWebhook(Request $request): bool
    {
        try {
            // PayUnit sends POST notification with payment status
            $payload = $request->all();

            Log::info('PayUnit webhook received', ['payload' => $payload]);

            // Verify required fields exist
            if (!isset($payload['data'])) {
                Log::error('PayUnit webhook missing data field');
                return false;
            }

            $data = $payload['data'];

            if (!isset($data['transaction_id']) || !isset($data['transaction_status'])) {
                Log::error('PayUnit webhook missing required fields', ['data' => $data]);
                return false;
            }

            // Additional verification can be added here
            // (e.g., verify source IP, signature if available)

            return true;

        } catch (\Exception $e) {
            Log::error('PayUnit webhook verification failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }
}
