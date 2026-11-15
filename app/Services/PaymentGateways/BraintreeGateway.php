<?php

namespace App\Services\PaymentGateways;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Order;
use Braintree\Gateway;
use Illuminate\Http\Request;

class BraintreeGateway implements PaymentGatewayInterface
{
    protected Gateway $gateway;

    public function __construct()
    {
        $this->gateway = new Gateway([
            'environment' => config('payment.gateways.braintree.environment'),
            'merchantId' => config('payment.gateways.braintree.merchant_id'),
            'publicKey' => config('payment.gateways.braintree.public_key'),
            'privateKey' => config('payment.gateways.braintree.private_key'),
        ]);
    }

    public function createPayment(Order $order, array $paymentData): array
    {
        try {
            $result = $this->gateway->transaction()->sale([
                'amount' => $order->total,
                'paymentMethodNonce' => $paymentData['payment_nonce'],
                'options' => [
                    'submitForSettlement' => true
                ],
                'customer' => [
                    'firstName' => $order->customer_first_name,
                    'lastName' => $order->customer_last_name,
                    'email' => $order->customer_email,
                    'phone' => $order->customer_phone,
                ],
                'billing' => [
                    'firstName' => $order->customer_first_name,
                    'lastName' => $order->customer_last_name,
                    'streetAddress' => $order->address_line_1,
                    'extendedAddress' => $order->address_line_2,
                    'locality' => $order->city,
                    'region' => $order->state,
                    'postalCode' => $order->postal_code,
                    'countryCodeAlpha2' => $order->country,
                ],
            ]);

            if ($result->success) {
                return [
                    'success' => true,
                    'transaction_id' => $result->transaction->id,
                    'status' => $result->transaction->status,
                    'response' => [
                        'id' => $result->transaction->id,
                        'status' => $result->transaction->status,
                        'amount' => $result->transaction->amount,
                        'currency' => $result->transaction->currencyIsoCode,
                        'created_at' => $result->transaction->createdAt->format('Y-m-d H:i:s'),
                    ],
                ];
            }

            return [
                'success' => false,
                'error' => $result->getMessage(),
                'response' => [
                    'message' => $result->getMessage(),
                    'errors' => $result->errors->deepAll(),
                ],
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'response' => [
                    'exception' => $e->getMessage(),
                ],
            ];
        }
    }

    public function capturePayment(string $transactionId): array
    {
        try {
            $result = $this->gateway->transaction()->submitForSettlement($transactionId);

            if ($result->success) {
                return [
                    'success' => true,
                    'transaction_id' => $result->transaction->id,
                    'status' => $result->transaction->status,
                ];
            }

            return [
                'success' => false,
                'error' => $result->getMessage(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function refundPayment(string $transactionId, float $amount): array
    {
        try {
            $result = $this->gateway->transaction()->refund($transactionId, $amount);

            if ($result->success) {
                return [
                    'success' => true,
                    'transaction_id' => $result->transaction->id,
                    'refund_id' => $result->transaction->refundId,
                ];
            }

            return [
                'success' => false,
                'error' => $result->getMessage(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function getClientToken(): ?string
    {
        try {
            return $this->gateway->clientToken()->generate();
        } catch (\Exception $e) {
            return null;
        }
    }

    public function verifyWebhook(Request $request): bool
    {
        try {
            $signature = $request->input('bt_signature');
            $payload = $request->input('bt_payload');

            $webhookNotification = $this->gateway->webhookNotification()->parse($signature, $payload);

            return $webhookNotification->kind !== null;
        } catch (\Exception $e) {
            return false;
        }
    }
}
