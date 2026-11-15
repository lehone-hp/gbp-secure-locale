<?php

namespace App\Services\PaymentGateways;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Order;
use Illuminate\Http\Request;
use Square\SquareClient;
use Square\Environments;
use Square\Payments\Requests\CreatePaymentRequest;
use Square\Payments\Requests\CompletePaymentRequest;
use Square\Refunds\Requests\RefundPaymentRequest;
use Square\Types\Money;
use Square\Exceptions\SquareApiException;

class SquareGateway implements PaymentGatewayInterface
{
    protected SquareClient $square;

    public function __construct()
    {
        $env = config('payment.gateways.square.environment', 'sandbox');
        $baseUrl = $env === 'production'
            ? Environments::Production->value
            : Environments::Sandbox->value;

        $this->square = new SquareClient(
            token: config('payment.gateways.square.access_token'),
            options: ['baseUrl' => $baseUrl],
        );
    }

    public function createPayment(Order $order, array $paymentData): array
    {
        try {
            $response = $this->square->payments->create(new CreatePaymentRequest([
                'sourceId'       => $paymentData['payment_source_id'],
                'idempotencyKey' => uniqid('sq_', true),
                'amountMoney'    => new Money([
                    'amount'   => (int) round($order->total * $this->exchangeRate($order->currency) * 100),
                    'currency' => config('payment.gateways.square.currency', strtoupper($order->currency)),
                ]),
                'locationId'         => config('payment.gateways.square.location_id'),
                'referenceId'        => (string) $order->id,
                'note'               => "Order #{$order->id}",
                'buyerEmailAddress'  => $order->customer_email,
                'verificationToken'  => $paymentData['verification_token'] ?? null,
            ]));

            $payment = $response->getPayment();

            if ($payment && in_array($payment->getStatus(), ['COMPLETED', 'AUTHORIZED'])) {
                return [
                    'success'        => true,
                    'transaction_id' => $payment->getId(),
                    'status'         => $payment->getStatus(),
                    'response'       => [
                        'id'         => $payment->getId(),
                        'status'     => $payment->getStatus(),
                        'amount'     => $order->total,
                        'currency'   => $order->currency,
                        'created_at' => $payment->getCreatedAt(),
                    ],
                ];
            }

            return [
                'success'  => false,
                'error'    => 'Payment failed',
                'response' => ['status' => $payment?->getStatus()],
            ];
        } catch (SquareApiException $e) {
            $errors = $e->getErrors();
            $message = !empty($errors) ? ($errors[0]->getDetail() ?? $errors[0]->getCode()) : $e->getMessage();

            return [
                'success'  => false,
                'error'    => $message,
                'response' => ['exception' => $e->getMessage()],
            ];
        } catch (\Exception $e) {
            return [
                'success'  => false,
                'error'    => $e->getMessage(),
                'response' => ['exception' => $e->getMessage()],
            ];
        }
    }

    public function capturePayment(string $transactionId): array
    {
        try {
            $response = $this->square->payments->complete(new CompletePaymentRequest([
                'paymentId' => $transactionId,
            ]));

            $payment = $response->getPayment();

            if ($payment && $payment->getStatus() === 'COMPLETED') {
                return [
                    'success'        => true,
                    'transaction_id' => $payment->getId(),
                    'status'         => $payment->getStatus(),
                ];
            }

            return ['success' => false, 'error' => 'Capture failed'];
        } catch (SquareApiException $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Orders already in the Square account currency are charged as-is; others
     * (USD orders) are converted with the admin-set square_exchange_rate.
     */
    private function exchangeRate(string $orderCurrency): float
    {
        return strtoupper($orderCurrency) === strtoupper(config('payment.gateways.square.currency', ''))
            ? 1.0
            : (float) settings('square_exchange_rate', 1.0);
    }

    public function refundPayment(string $transactionId, float $amount): array
    {
        try {
            $response = $this->square->refunds->refundPayment(new RefundPaymentRequest([
                'idempotencyKey' => uniqid('sqr_', true),
                'amountMoney'    => new Money([
                    'amount'   => (int) round($amount * (float) settings('square_exchange_rate', 1.0) * 100),
                    'currency' => config('payment.gateways.square.currency', 'EUR'),
                ]),
                'paymentId' => $transactionId,
            ]));

            $refund = $response->getRefund();

            if ($refund && in_array($refund->getStatus(), ['COMPLETED', 'PENDING'])) {
                return [
                    'success'        => true,
                    'transaction_id' => $transactionId,
                    'refund_id'      => $refund->getId(),
                ];
            }

            return ['success' => false, 'error' => 'Refund failed'];
        } catch (SquareApiException $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function getClientToken(): ?string
    {
        return config('payment.gateways.square.application_id');
    }

    public function verifyWebhook(Request $request): bool
    {
        $sigKey = config('payment.gateways.square.webhook_secret');

        if (!$sigKey) {
            return false;
        }

        $payload         = $request->getContent();
        $notificationUrl = url('/webhooks/square');
        $expected        = base64_encode(hash_hmac('sha256', $notificationUrl . $payload, $sigKey, true));

        return hash_equals($expected, $request->header('x-square-hmacsha256-signature', ''));
    }
}
