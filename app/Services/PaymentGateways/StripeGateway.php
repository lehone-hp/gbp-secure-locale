<?php

namespace App\Services\PaymentGateways;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Order;
use Illuminate\Http\Request;
use Stripe\PaymentIntent;
use Stripe\StripeClient;
use Stripe\Exception\ApiErrorException;

class StripeGateway implements PaymentGatewayInterface
{
    protected StripeClient $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('payment.gateways.stripe.secret'));
    }

    public function createPayment(Order $order, array $paymentData): array
    {
        try {
            $paymentIntent = $this->stripe->paymentIntents->create([
                'amount' => $order->total * 100,
                'currency' => strtolower($order->currency),
                'payment_method' => $paymentData['payment_method_id'],
                'confirm' => true,
                // Ask for a 3D Secure next action that Stripe.js can handle in a modal
                'use_stripe_sdk' => true,
                'description' => "Order #{$order->id}",
                'metadata' => [
                    'order_id' => $order->id,
                    'customer_email' => $order->customer_email,
                ],
                'receipt_email' => $order->customer_email,
                'return_url' => route('checkout.success', $order->id),
            ]);

            if ($paymentIntent->status === 'succeeded') {
                return [
                    'success' => true,
                    'transaction_id' => $paymentIntent->id,
                    'status' => $paymentIntent->status,
                    'response' => $this->summarise($paymentIntent),
                ];
            }

            if ($paymentIntent->status === 'requires_action') {
                return [
                    'success' => false,
                    'requires_action' => true,
                    'transaction_id' => $paymentIntent->id,
                    'client_secret' => $paymentIntent->client_secret,
                    'error' => '3D Secure authentication required',
                ];
            }

            return [
                'success' => false,
                'error' => 'Payment failed',
                'response' => [
                    'status' => $paymentIntent->status,
                ],
            ];
        } catch (ApiErrorException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'response' => [
                    'exception' => $e->getMessage(),
                    'type' => $e->getError()->type ?? null,
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

    /**
     * Ask Stripe for the current state of a PaymentIntent, e.g. once the
     * customer returns from 3D Secure, rather than trusting the browser.
     */
    public function retrievePayment(string $transactionId): array
    {
        try {
            $paymentIntent = $this->stripe->paymentIntents->retrieve($transactionId);

            return [
                'success' => $paymentIntent->status === 'succeeded',
                'transaction_id' => $paymentIntent->id,
                'status' => $paymentIntent->status,
                'response' => $this->summarise($paymentIntent),
            ];
        } catch (ApiErrorException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    protected function summarise(PaymentIntent $paymentIntent): array
    {
        return [
            'id' => $paymentIntent->id,
            'status' => $paymentIntent->status,
            'amount' => $paymentIntent->amount / 100,
            'currency' => strtoupper($paymentIntent->currency),
            'created_at' => date('Y-m-d H:i:s', $paymentIntent->created),
        ];
    }

    public function capturePayment(string $transactionId): array
    {
        try {
            $paymentIntent = $this->stripe->paymentIntents->capture($transactionId);

            if ($paymentIntent->status === 'succeeded') {
                return [
                    'success' => true,
                    'transaction_id' => $paymentIntent->id,
                    'status' => $paymentIntent->status,
                ];
            }

            return [
                'success' => false,
                'error' => 'Capture failed',
            ];
        } catch (ApiErrorException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function refundPayment(string $transactionId, float $amount): array
    {
        try {
            $refund = $this->stripe->refunds->create([
                'payment_intent' => $transactionId,
                'amount' => $amount * 100,
            ]);

            if ($refund->status === 'succeeded') {
                return [
                    'success' => true,
                    'transaction_id' => $transactionId,
                    'refund_id' => $refund->id,
                ];
            }

            return [
                'success' => false,
                'error' => 'Refund failed',
            ];
        } catch (ApiErrorException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function getClientToken(): ?string
    {
        return config('payment.gateways.stripe.key');
    }

    public function verifyWebhook(Request $request): bool
    {
        try {
            $payload = $request->getContent();
            $signature = $request->header('Stripe-Signature');
            $webhookSecret = config('payment.gateways.stripe.webhook_secret');

            \Stripe\Webhook::constructEvent($payload, $signature, $webhookSecret);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
