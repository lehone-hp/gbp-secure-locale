<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Concerns\SendsOrderEmails;
use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutRequest;
use App\Http\Requests\StoreCheckoutRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ServiceAddon;
use App\Services\CurrencyManager;
use App\Services\PaymentGatewayManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

class CheckoutController extends Controller
{
    use SendsOrderEmails;

    protected PaymentGatewayManager $paymentManager;

    protected CurrencyManager $currencies;

    public function __construct(PaymentGatewayManager $paymentManager, CurrencyManager $currencies)
    {
        $this->paymentManager = $paymentManager;
        $this->currencies = $currencies;
    }

    /**
     * Step 1: customer details for the selected add-on.
     */
    public function index(Request $request)
    {
        $addon = ServiceAddon::with('service')->find($request->query('addon'));

        if (! $addon) {
            return redirect()->route('services.index')->with('error', __('checkout.select_addon_first'));
        }

        // Prefill from the pending order this session already started, otherwise demo data in development
        $existing = $this->sessionOrder($addon);
        $demoData = $existing ? [
            'first_name' => $existing->customer_first_name,
            'last_name' => $existing->customer_last_name,
            'email' => $existing->customer_email,
            'phone' => $existing->customer_phone,
            'company' => $existing->customer_company,
            'address_line_1' => $existing->address_line_1,
            'address_line_2' => $existing->address_line_2,
            'city' => $existing->city,
            'state' => $existing->state,
            'postal_code' => $existing->postal_code,
            'country' => $existing->country,
            'notes' => $existing->notes,
        ] : null;

        if (! $demoData && (config('app.env') === 'local' || config('app.debug'))) {
            $demoData = [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john.doe@example.com',
                'phone' => '+1 (555) 123-4567',
                'company' => 'Acme Corporation',
                'address_line_1' => '123 Main Street',
                'address_line_2' => 'Suite 100',
                'city' => 'New York',
                'state' => 'NY',
                'postal_code' => '10001',
                'country' => 'US',
                'notes' => 'This is a test order for development purposes.',
            ];
        }

        $chargeCurrency = $this->paymentManager->chargeCurrency($this->currencies->current());

        return view('web.services.checkout', [
            'addon' => $addon,
            'chargeCurrency' => $chargeCurrency,
            'chargePrice' => $this->currencies->convert((float) $addon->price, $chargeCurrency),
            'currencyFallback' => $chargeCurrency !== $this->currencies->current(),
            'demoData' => $demoData,
            'countries' => config('constants.countries'),
        ]);
    }

    /**
     * Step 1 submit: create (or update) the pending order and move to payment.
     */
    public function start(StoreCheckoutRequest $request)
    {
        $addon = ServiceAddon::findOrFail($request->addon_id);

        $currency = $this->paymentManager->chargeCurrency($this->currencies->current());
        $rate = $this->currencies->rate($currency);

        $order = DB::transaction(function () use ($request, $addon, $currency, $rate) {
            $order = $this->sessionOrder($addon);

            if ($order) {
                $order->update($this->customerAttributes($request));

                // The visitor may have switched currency since this order was priced
                if ($order->currency !== $currency) {
                    $order->update(['currency' => $currency, 'exchange_rate' => $rate]);
                    $order->items->each(fn (OrderItem $item) => $item->update(['price' => $this->currencies->convert((float) $addon->price, $currency)]));
                    $order->calculateTotals();
                }

                return $order;
            }

            $order = Order::create($this->customerAttributes($request) + [
                'subtotal' => 0,
                'total' => 0,
                'currency' => $currency,
                'exchange_rate' => $rate,
                'status' => Order::STATUS_PENDING,
                'payment_gateway' => $this->paymentManager->getCurrentGateway(),
                'domain' => $request->getHost(),
                'payment_status' => Order::PAYMENT_STATUS_PENDING,
            ]);

            $price = $this->currencies->convert((float) $addon->price, $currency);

            OrderItem::create([
                'order_id' => $order->id,
                'service_addon_id' => $addon->id,
                'item_name' => $addon->name,
                'item_description' => $addon->description,
                'item_type' => $addon->type,
                'price' => $price,
                'quantity' => 1,
                'subtotal' => $price,
            ]);
            $order->calculateTotals();

            return $order;
        });

        session(['checkout_order_id' => $order->id]);

        return redirect(URL::signedRoute('checkout.pay', $order, absolute: false));
    }

    /**
     * Step 2: order summary and payment for an existing order.
     */
    public function pay(Order $order)
    {
        if ($order->payment_status === Order::PAYMENT_STATUS_PAID) {
            return redirect()->route('checkout.success', $order);
        }

        $resolvedGateway = $this->paymentManager->getCurrentGateway();
        // The view only distinguishes "paypal-style checkout" vs. other gateways;
        // which PayPal account is active is decided server-side via config overrides.
        $paymentGateway = in_array($resolvedGateway, Order::PAYPAL_GATEWAY_VARIANTS, true)
            ? Order::PAYMENT_GATEWAY_PAYPAL
            : $resolvedGateway;

        $order->load('items');

        return view('web.services.checkout-pay', [
            'order' => $order,
            'paymentGateway' => $paymentGateway,
            'customer' => [
                'name' => $order->customer_full_name,
                'first_name' => $order->customer_first_name,
                'last_name' => $order->customer_last_name,
                'email' => $order->customer_email,
                'phone' => $order->customer_phone,
                'address_line_1' => $order->address_line_1,
                'address_line_2' => $order->address_line_2,
                'city' => $order->city,
                'state' => $order->state,
                'postal_code' => $order->postal_code,
                'country' => $order->country,
            ],
            'processUrl' => URL::signedRoute('checkout.process', $order, absolute: false),
            'paypalCreateUrl' => URL::signedRoute('checkout.paypal.create', $order, absolute: false),
            'editUrl' => route('checkout.index', ['addon' => $order->items->first()?->service_addon_id]),
        ]);
    }

    public function process(CheckoutRequest $request, Order $order)
    {
        if (! $this->isPayable($order)) {
            return $this->paymentError(__('checkout.order_not_payable'), 409);
        }

        try {
            // A declined attempt leaves the order failed; paying again starts a fresh attempt
            $order->update([
                'payment_gateway' => $this->paymentManager->getCurrentGateway(),
                'status' => Order::STATUS_PENDING,
                'payment_status' => Order::PAYMENT_STATUS_PENDING,
            ]);

            $paymentResult = $this->paymentManager->driver()->createPayment($order, [
                'payment_nonce'      => $request->payment_nonce,
                'payment_method_id'  => $request->payment_method_id,
                'payment_source_id'  => $request->payment_source_id,
                'verification_token' => $request->verification_token,
            ]);

            // 3D Secure: the browser finishes authentication with Stripe.js and
            // then lands on checkout.success, so the order must stay pending
            if ($paymentResult['requires_action'] ?? false) {
                $order->update(['payment_transaction_id' => $paymentResult['transaction_id']]);

                return response()->json([
                    'requires_action' => true,
                    'client_secret' => $paymentResult['client_secret'],
                    'redirect_url' => route('checkout.success', $order->id),
                ]);
            }

            if ($paymentResult['success']) {
                $order->update([
                    'payment_transaction_id' => $paymentResult['transaction_id'] ?? null,
                    'payment_response' => $paymentResult['response'] ?? null,
                ]);

                // Whop: the browser mounts the embedded checkout; the webhook marks the order paid
                if ($order->payment_gateway === Order::PAYMENT_GATEWAY_WHOP) {
                    return response()->json([
                        'embedded_checkout_id' => $paymentResult['checkout_configuration_id'],
                        'redirect_url' => route('checkout.success', $order->id),
                    ]);
                }

                // PayUnit: redirect to the hosted payment page
                if ($order->payment_gateway === Order::PAYMENT_GATEWAY_PAYUNIT && isset($paymentResult['hosted_url'])) {
                    return response()->json(['redirect_url' => $paymentResult['hosted_url']]);
                }

                // PayPal: redirect to the approval URL (customer needs to approve)
                if (in_array($order->payment_gateway, Order::PAYPAL_GATEWAY_VARIANTS, true) && isset($paymentResult['approval_url'])) {
                    return response()->json(['redirect_url' => $paymentResult['approval_url']]);
                }

                // Braintree/Stripe: payment is already captured
                if ($order->markAsPaidIfPending(
                    $paymentResult['transaction_id'] ?? null,
                    $paymentResult['response'] ?? null
                )) {
                    $this->sendOrderEmails($order);
                }

                return response()->json([
                    'success' => true,
                    'redirect_url' => route('checkout.success', $order->id),
                ]);
            }

            $order->markAsFailed($paymentResult['response'] ?? null);

            return $this->paymentError($paymentResult['error'] ?? __('messages.payment_failed'), 422);

        } catch (\Exception $e) {
            \Log::error('Order processing failed: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->paymentError(__('messages.order_processing_error'), 500);
        }
    }

    public function success(Order $order)
    {
        // If payment is already paid, just show success page
        if ($order->payment_status === Order::PAYMENT_STATUS_PAID) {
            return view('web.services.checkout-success', compact('order'));
        }

        // For PayUnit and Whop, payment confirmation happens via webhook
        // Show pending page while waiting for webhook confirmation
        if (in_array($order->payment_gateway, [Order::PAYMENT_GATEWAY_PAYUNIT, Order::PAYMENT_GATEWAY_WHOP], true) && $order->payment_status === Order::PAYMENT_STATUS_PENDING) {
            return view('web.services.checkout-success', compact('order'))
                ->with('info', __('messages.payment_processing'));
        }

        // For PayPal, capture the payment when customer returns from approval
        if (in_array($order->payment_gateway, Order::PAYPAL_GATEWAY_VARIANTS, true) && $order->payment_status === Order::PAYMENT_STATUS_PENDING) {
            try {
                $paymentGateway = $this->paymentManager->driver($order->payment_gateway);

                $captureResult = $paymentGateway->capturePayment($order->payment_transaction_id);

                if ($captureResult['success']) {
                    $order->markAsPaid(
                        $order->payment_transaction_id,
                        array_merge($order->payment_response ?? [], ['capture' => $captureResult])
                    );

                    $this->sendOrderEmails($order);

                    return view('web.services.checkout-success', compact('order'))
                        ->with('success', __('messages.payment_success'));
                }

                // Capture failed
                $order->markAsFailed($captureResult);

                return redirect()
                    ->route('checkout.cancel')
                    ->with('error', $captureResult['error'] ?? __('messages.payment_failed'));

            } catch (\Exception $e) {
                \Log::error('PayPal capture failed: ' . $e->getMessage());

                return redirect()
                    ->route('checkout.cancel')
                    ->with('error', __('messages.payment_failed'));
            }
        }

        // For Stripe, confirm with Stripe that 3D Secure completed. A failed check
        // leaves the order pending: the webhook is the source of truth, and this
        // GET route must not fail an order whose authentication is still open.
        if ($order->payment_gateway === Order::PAYMENT_GATEWAY_STRIPE && $order->payment_status === Order::PAYMENT_STATUS_PENDING && $order->payment_transaction_id) {
            $paymentResult = $this->paymentManager->driver($order->payment_gateway)
                ->retrievePayment($order->payment_transaction_id);

            if ($paymentResult['success']) {
                if ($order->markAsPaidIfPending($order->payment_transaction_id, $paymentResult['response'])) {
                    $this->sendOrderEmails($order);
                }

                return view('web.services.checkout-success', compact('order'))
                    ->with('success', __('messages.payment_success'));
            }

            return redirect()
                ->route('checkout.cancel')
                ->with('error', $paymentResult['error'] ?? __('messages.payment_failed'));
        }

        // Not paid and nothing to confirm: send the buyer back to the payment step
        return redirect(URL::signedRoute('checkout.pay', $order, absolute: false));
    }

    public function cancel()
    {
        return view('web.services.checkout-cancel');
    }

    /**
     * Create the PayPal order for the existing order (inline payment via AJAX)
     */
    public function createPayPalOrder(Order $order)
    {
        if (! $this->isPayable($order)) {
            return $this->paymentError(__('checkout.order_not_payable'), 409);
        }

        try {
            // Resolve which PayPal variant this domain is actually assigned to
            // (falls back to the default PayPal account if the tenant isn't on one).
            $gateway = $this->paymentManager->getCurrentGateway();
            if (!in_array($gateway, Order::PAYPAL_GATEWAY_VARIANTS, true)) {
                $gateway = Order::PAYMENT_GATEWAY_PAYPAL;
            }

            $order->update([
                'payment_gateway' => $gateway,
                'status' => Order::STATUS_PENDING,
                'payment_status' => Order::PAYMENT_STATUS_PENDING,
            ]);

            $paymentResult = $this->paymentManager->driver($gateway)->createPayment($order, []);

            if ($paymentResult['success']) {
                $order->update([
                    'payment_transaction_id' => $paymentResult['transaction_id'],
                    'payment_response' => $paymentResult['response'],
                ]);

                return response()->json([
                    'success' => true,
                    'order_id' => $paymentResult['transaction_id'],
                    'internal_order_id' => $order->id,
                ]);
            }

            return $this->paymentError($paymentResult['error'] ?? __('checkout.api_create_order_failed'), 400);

        } catch (\Exception $e) {
            \Log::error('PayPal order creation failed: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->paymentError(__('checkout.api_create_order_exception'), 500);
        }
    }

    /**
     * Capture PayPal order via AJAX
     */
    public function capturePayPalOrder(Request $request)
    {
        try {
            $request->validate([
                'order_id' => 'required|string',
            ]);

            $paypalOrderId = $request->order_id;

            // Find our internal order
            $order = Order::where('payment_transaction_id', $paypalOrderId)
                ->whereIn('payment_gateway', Order::PAYPAL_GATEWAY_VARIANTS)
                ->firstOrFail();

            // Capture the payment
            $paymentGateway = $this->paymentManager->driver($order->payment_gateway);
            $captureResult = $paymentGateway->capturePayment($paypalOrderId);

            if ($captureResult['success']) {
                $order->markAsPaid(
                    $paypalOrderId,
                    array_merge($order->payment_response ?? [], ['capture' => $captureResult])
                );

                $this->sendOrderEmails($order);

                return response()->json([
                    'success' => true,
                    'order_id' => $order->id,
                    'redirect_url' => route('checkout.success', $order->id),
                ]);
            }

            $order->markAsFailed($captureResult);

            return response()->json([
                'success' => false,
                'error' => $captureResult['error'] ?? __('checkout.api_capture_payment_failed'),
            ], 400);

        } catch (\Exception $e) {
            \Log::error('PayPal capture failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => __('checkout.api_capture_payment_failed') . ': ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Every payment endpoint answers JSON so the page can always show the message.
     */
    protected function paymentError(string $error, int $status)
    {
        return response()->json(['success' => false, 'error' => $error], $status);
    }

    protected function isPayable(Order $order): bool
    {
        return in_array($order->payment_status, [Order::PAYMENT_STATUS_PENDING, Order::PAYMENT_STATUS_FAILED], true);
    }

    /**
     * The still-unpaid order this session started for the given add-on, if any.
     */
    protected function sessionOrder(ServiceAddon $addon): ?Order
    {
        $order = Order::whereIn('payment_status', [Order::PAYMENT_STATUS_PENDING, Order::PAYMENT_STATUS_FAILED])->find(session('checkout_order_id'));

        return $order && $order->items()->where('service_addon_id', $addon->id)->exists() ? $order : null;
    }

    protected function customerAttributes(Request $request): array
    {
        return [
            'customer_first_name' => $request->first_name,
            'customer_last_name' => $request->last_name,
            'customer_email' => $request->email,
            'customer_phone' => $request->phone,
            'customer_company' => $request->company,
            'address_line_1' => $request->address_line_1,
            'address_line_2' => $request->address_line_2,
            'city' => $request->city,
            'state' => $request->state,
            'postal_code' => $request->postal_code,
            'country' => $request->country,
            'notes' => $request->notes,
            'terms_accepted_at' => now(),
            'immediate_start_accepted' => true,
            'policy_version' => config('legal.refund_version'),
        ];
    }
}
