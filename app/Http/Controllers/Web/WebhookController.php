<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Concerns\SendsOrderEmails;
use App\Http\Controllers\Controller;
use App\Mail\NewOrderNotification;
use App\Mail\OrderConfirmation;
use App\Models\Order;
use App\Services\PaymentGatewayManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class WebhookController extends Controller
{
    use SendsOrderEmails;

    protected PaymentGatewayManager $paymentManager;

    public function __construct(PaymentGatewayManager $paymentManager)
    {
        $this->paymentManager = $paymentManager;
    }

    public function braintree(Request $request)
    {
        try {
            $gateway = $this->paymentManager->driver(Order::PAYMENT_GATEWAY_BRAINTREE);
            
            if (!$gateway->verifyWebhook($request)) {
                Log::warning('Braintree webhook verification failed');
                return response()->json(['error' => 'Invalid signature'], 403);
            }

            Log::info('Braintree webhook received', $request->all());

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Braintree webhook error: ' . $e->getMessage());
            return response()->json(['error' => 'Webhook processing failed'], 500);
        }
    }

    public function stripe(Request $request)
    {
        try {
            $gateway = $this->paymentManager->driver(Order::PAYMENT_GATEWAY_STRIPE);
            
            if (!$gateway->verifyWebhook($request)) {
                Log::warning('Stripe webhook verification failed');
                return response()->json(['error' => 'Invalid signature'], 403);
            }

            $payload = json_decode($request->getContent(), true);
            
            Log::info('Stripe webhook received', ['type' => $payload['type'] ?? 'unknown']);

            if (isset($payload['type'])) {
                $this->handleStripeEvent($payload);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Stripe webhook error: ' . $e->getMessage());
            return response()->json(['error' => 'Webhook processing failed'], 500);
        }
    }

    public function paypal(Request $request)
    {
        return $this->handlePaypalWebhook($request, Order::PAYMENT_GATEWAY_PAYPAL);
    }

    /**
     * Webhook endpoint for the second PayPal account - register this URL (not
     * /webhooks/paypal) as that app's webhook subscription target in PayPal's
     * developer dashboard so it verifies with PAYPAL_WEBHOOK_ID_2.
     */
    public function paypal2(Request $request)
    {
        return $this->handlePaypalWebhook($request, Order::PAYMENT_GATEWAY_PAYPAL2);
    }

    protected function handlePaypalWebhook(Request $request, string $gateway)
    {
        try {
            if ($gateway === Order::PAYMENT_GATEWAY_PAYPAL2) {
                Config::set('paypal', config('paypal2'));
                Config::set('payment.gateways.paypal.webhook_id', config('paypal2.webhook_id'));
            }

            $driver = $this->paymentManager->driver(Order::PAYMENT_GATEWAY_PAYPAL);

            if (!$driver->verifyWebhook($request)) {
                Log::warning('PayPal webhook verification failed', ['gateway' => $gateway]);
                return response()->json(['error' => 'Invalid signature'], 403);
            }

            Log::info('PayPal webhook received', ['gateway' => $gateway] + $request->all());

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('PayPal webhook error: ' . $e->getMessage());
            return response()->json(['error' => 'Webhook processing failed'], 500);
        }
    }

    public function payunit(Request $request)
    {
        try {
            $gateway = $this->paymentManager->driver(Order::PAYMENT_GATEWAY_PAYUNIT);
            
            if (!$gateway->verifyWebhook($request)) {
                Log::error('PayUnit webhook verification failed');
                return response()->json(['error' => 'Invalid webhook'], 400);
            }

            $payload = $request->all();
            $transactionId = $payload['data']['transaction_id'] ?? null;
            $status = $payload['data']['transaction_status'] ?? null;
            $purchaseRef = $payload['data']['purchaseRef'] ?? null;

            if (!$transactionId || !$status) {
                Log::error('PayUnit webhook missing required data');
                return response()->json(['error' => 'Invalid payload'], 400);
            }

            // Find order by transaction ID or purchase reference
            $order = Order::where('payment_transaction_id', $transactionId)
                ->orWhere('id', $purchaseRef)
                ->first();

            if (!$order) {
                Log::error('PayUnit webhook: Order not found', ['transaction_id' => $transactionId]);
                return response()->json(['error' => 'Order not found'], 404);
            }

            // Update order based on payment status
            switch ($status) {
                case 'SUCCESS':
                    $order->markAsPaid($transactionId, $payload);
                    
                    // Send confirmation emails
                    Mail::to($order->customer_email)->send(new OrderConfirmation($order));
                    
                    $adminEmail = config('mail.admin_email', config('mail.from.address'));
                    Mail::to($adminEmail)->send(new NewOrderNotification($order));
                    
                    Log::info('PayUnit payment successful', ['order_id' => $order->id]);
                    break;

                case 'FAILED':
                case 'CANCELLED':
                    $order->markAsFailed($payload);
                    Log::warning('PayUnit payment failed/cancelled', ['order_id' => $order->id, 'status' => $status]);
                    break;

                default:
                    Log::warning('PayUnit unknown status', ['status' => $status, 'order_id' => $order->id]);
            }

            return response()->json(['success' => true], 200);

        } catch (\Exception $e) {
            Log::error('PayUnit webhook processing failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Processing failed'], 500);
        }
    }

    public function whop(Request $request)
    {
        try {
            if (!$this->paymentManager->driver(Order::PAYMENT_GATEWAY_WHOP)->verifyWebhook($request)) {
                Log::error('Whop webhook verification failed');
                return response()->json(['error' => 'Invalid webhook'], 403);
            }

            // Whop delivers at least once; the same webhook-id is reused on retries
            if (!Cache::add('whop_webhook_' . $request->header('webhook-id'), true, now()->addDays(4))) {
                return response()->json(['success' => true]);
            }

            $type = $request->input('type');
            $payment = $request->input('data', []);
            $order = Order::find($payment['metadata']['order_id'] ?? null);

            if (!$order || !in_array($type, ['payment.succeeded', 'payment.failed'], true)) {
                return response()->json(['success' => true]);
            }

            if ($type === 'payment.succeeded') {
                if ($order->markAsPaidIfPending($payment['id'] ?? null, $request->all())) {
                    $this->sendOrderEmails($order);
                }
            } elseif ($order->payment_status === Order::PAYMENT_STATUS_PENDING) {
                // Whop's embedded checkout lets the buyer retry, so keep the order pending
                Log::warning('Whop payment failed', ['order_id' => $order->id]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Whop webhook processing failed: ' . $e->getMessage());
            return response()->json(['error' => 'Processing failed'], 500);
        }
    }

    public function square(Request $request)
    {
        try {
            $gateway = $this->paymentManager->driver(Order::PAYMENT_GATEWAY_SQUARE);

            if (!$gateway->verifyWebhook($request)) {
                Log::warning('Square webhook verification failed');
                return response()->json(['error' => 'Invalid signature'], 403);
            }

            $payload = json_decode($request->getContent(), true);

            Log::info('Square webhook received', ['type' => $payload['type'] ?? 'unknown']);

            $this->handleSquareEvent($payload);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Square webhook error: ' . $e->getMessage());
            return response()->json(['error' => 'Webhook processing failed'], 500);
        }
    }

    protected function handleSquareEvent(array $payload): void
    {
        $type       = $payload['type'] ?? null;
        $paymentObj = $payload['data']['object']['payment'] ?? null;

        if (!$paymentObj) {
            return;
        }

        $orderId       = $paymentObj['reference_id'] ?? null;
        $transactionId = $paymentObj['id'] ?? null;
        $status        = $paymentObj['status'] ?? null;

        if (!$orderId) {
            return;
        }

        $order = Order::find($orderId);

        if (!$order) {
            Log::error('Square webhook: Order not found', ['reference_id' => $orderId]);
            return;
        }

        if ($type === 'payment.updated' && $status === 'COMPLETED') {
            if ($order->payment_status === Order::PAYMENT_STATUS_PENDING) {
                $order->markAsPaid($transactionId, $paymentObj);

                Mail::to($order->customer_email)->send(new \App\Mail\OrderConfirmation($order));

                $adminEmail = config('mail.admin_email', config('mail.from.address'));
                Mail::to($adminEmail)->send(new \App\Mail\NewOrderNotification($order));

                Log::info('Square payment completed via webhook', ['order_id' => $order->id]);
            }
        } elseif ($type === 'payment.updated' && in_array($status, ['FAILED', 'CANCELED'])) {
            if ($order->payment_status === Order::PAYMENT_STATUS_PENDING) {
                $order->markAsFailed($paymentObj);
                Log::warning('Square payment failed/canceled via webhook', ['order_id' => $order->id, 'status' => $status]);
            }
        }
    }

    protected function handleStripeEvent(array $payload): void
    {
        switch ($payload['type']) {
            case 'payment_intent.succeeded':
                $this->handlePaymentIntentSucceeded($payload['data']['object']);
                break;
            case 'payment_intent.payment_failed':
                $this->handlePaymentIntentFailed($payload['data']['object']);
                break;
        }
    }

    protected function handlePaymentIntentSucceeded(array $paymentIntent): void
    {
        $orderId = $paymentIntent['metadata']['order_id'] ?? null;
        
        if ($orderId) {
            $order = Order::find($orderId);
            if ($order && $order->markAsPaidIfPending($paymentIntent['id'], $paymentIntent)) {
                $this->sendOrderEmails($order);
                Log::info("Order #{$orderId} marked as paid via webhook");
            }
        }
    }

    protected function handlePaymentIntentFailed(array $paymentIntent): void
    {
        $orderId = $paymentIntent['metadata']['order_id'] ?? null;
        
        if ($orderId) {
            $order = Order::find($orderId);
            if ($order && $order->payment_status === Order::PAYMENT_STATUS_PENDING) {
                $order->markAsFailed($paymentIntent);
                Log::info("Order #{$orderId} marked as failed via webhook");
            }
        }
    }
}
