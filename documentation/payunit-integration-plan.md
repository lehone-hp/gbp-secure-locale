# PayUnit Payment Gateway Integration Plan

## Executive Summary

This document outlines the complete implementation plan for integrating **PayUnit** as a payment gateway into the Conformite GBP checkout system, following the existing architecture patterns used for Stripe, Braintree, and PayPal.

**PayUnit** is an African payment aggregator that supports multiple payment methods including mobile money (Orange Money, MTN Mobile Money, etc.), cards (Visa/Mastercard), and other local payment providers across African countries.

---

## 1. PayUnit Overview

### 1.1 What is PayUnit?

- **Type:** Payment aggregator/gateway
- **Region:** Primarily African markets (Cameroon, etc.)
- **Payment Methods:** Mobile money, cards, bank transfers
- **Integration Type:** REST API + Hosted payment page
- **PHP SDK:** Available via Composer (`sevencommonfactor/payunit`)

### 1.2 Payment Flow

```
1. Merchant initializes payment via API
   ↓
2. PayUnit returns hosted payment URL
   ↓
3. Customer redirected to PayUnit hosted page
   ↓
4. Customer selects payment method and completes payment
   ↓
5. PayUnit sends webhook notification to merchant
   ↓
6. Customer redirected back to merchant site
   ↓
7. Merchant verifies payment status
```

### 1.3 Key Differences from Existing Gateways

| Feature | Stripe/Braintree | PayPal | PayUnit |
|---------|------------------|--------|---------|
| **UI Integration** | Elements/Drop-in | Buttons + Card Fields | Hosted page only |
| **Payment Flow** | Direct API | Hybrid | Redirect-based |
| **Capture** | Immediate | Deferred | Immediate |
| **Refunds** | API-based | API-based | Manual/API |
| **Webhooks** | Signature verification | Signature verification | POST notification |

---

## 2. Architecture Analysis

### 2.1 Existing Payment Gateway Interface

```php
interface PaymentGatewayInterface
{
    public function createPayment(Order $order, array $paymentData): array;
    public function capturePayment(string $transactionId): array;
    public function refundPayment(string $transactionId, float $amount): array;
    public function getClientToken(): ?string;
    public function verifyWebhook(Request $request): bool;
}
```

### 2.2 PayUnit API Mapping

| Interface Method | PayUnit Implementation |
|------------------|------------------------|
| `createPayment()` | Initialize transaction → Return hosted URL |
| `capturePayment()` | Not applicable (auto-captured) |
| `refundPayment()` | To be implemented (check API docs) |
| `getClientToken()` | Not applicable (no client-side SDK) |
| `verifyWebhook()` | Validate POST notification payload |

### 2.3 PayUnit API Credentials

Required environment variables:
- `PAYUNIT_API_KEY` - Application key (live/sandbox)
- `PAYUNIT_API_USERNAME` - Merchant API username
- `PAYUNIT_API_PASSWORD` - Merchant API password
- `PAYUNIT_MODE` - Environment mode (live/sandbox)

---

## 3. Implementation Plan

### Phase 1: Backend Gateway Implementation ⏳

#### Step 1.1: Install PayUnit PHP SDK

```bash
composer require sevencommonfactor/payunit
```

**Files to modify:**
- `composer.json` - Add dependency

#### Step 1.2: Create PayUnitGateway Class

**File:** `app/Services/PaymentGateways/PayUnitGateway.php`

```php
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
    protected string $baseUrl = 'https://gateway.payunit.net';

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

            if ($response['status'] === 'SUCCESS') {
                return [
                    'success' => true,
                    'transaction_id' => $response['data']['transaction_id'],
                    'hosted_url' => $response['data']['transaction_url'],
                    'response' => $response
                ];
            }

            return [
                'success' => false,
                'error' => $response['message'] ?? 'Payment initialization failed',
                'response' => $response
            ];

        } catch (\Exception $e) {
            Log::error('PayUnit payment creation failed: ' . $e->getMessage());
            
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
        // We just verify the payment status via webhook data
        
        return [
            'success' => true,
            'transaction_id' => $transactionId,
            'message' => 'Payment already captured by PayUnit'
        ];
    }

    public function refundPayment(string $transactionId, float $amount): array
    {
        // TODO: Implement refund API if available
        // Check PayUnit API documentation for refund endpoint
        
        Log::warning('PayUnit refund not implemented for transaction: ' . $transactionId);
        
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
            
            // Verify required fields exist
            if (!isset($payload['data']['transaction_id']) || 
                !isset($payload['data']['transaction_status'])) {
                Log::error('PayUnit webhook missing required fields');
                return false;
            }

            // Additional verification can be added here
            // (e.g., verify source IP, signature if available)
            
            return true;

        } catch (\Exception $e) {
            Log::error('PayUnit webhook verification failed: ' . $e->getMessage());
            return false;
        }
    }
}
```

**Key Implementation Notes:**
- Uses PayUnit PHP SDK for API communication
- Returns `hosted_url` for redirect-based payment flow
- Auto-capture model (no separate capture step)
- Webhook verification validates payload structure

#### Step 1.3: Update Configuration

**File:** `config/payment.php`

```php
'gateways' => [
    // ... existing gateways ...
    
    'payunit' => [
        'api_key' => env('PAYUNIT_API_KEY'),
        'api_username' => env('PAYUNIT_API_USERNAME'),
        'api_password' => env('PAYUNIT_API_PASSWORD'),
        'mode' => env('PAYUNIT_MODE', 'sandbox'),
    ],
],
```

#### Step 1.4: Update Order Model Constants

**File:** `app/Models/Order.php`

```php
const PAYMENT_GATEWAY_PAYUNIT = 'payunit';

// Update validation rules
protected $fillable = [
    // ... existing fields ...
    'payment_gateway', // Ensure this accepts 'payunit'
];
```

#### Step 1.5: Update PaymentGatewayManager

**File:** `app/Services/PaymentGatewayManager.php`

```php
use App\Services\PaymentGateways\PayUnitGateway;

public function driver(?string $gateway = null): PaymentGatewayInterface
{
    $gateway = $gateway ?? $this->resolveGateway();

    return match($gateway) {
        Order::PAYMENT_GATEWAY_BRAINTREE => app(BraintreeGateway::class),
        Order::PAYMENT_GATEWAY_STRIPE => app(StripeGateway::class),
        Order::PAYMENT_GATEWAY_PAYPAL => app(PayPalGateway::class),
        Order::PAYMENT_GATEWAY_PAYUNIT => app(PayUnitGateway::class),
        default => throw new InvalidArgumentException("Payment gateway [{$gateway}] is not supported.")
    };
}
```

#### Step 1.6: Update CheckoutController

**File:** `app/Http/Controllers/Web/CheckoutController.php`

Modify `process()` method to handle PayUnit redirect:

```php
public function process(CheckoutRequest $request)
{
    try {
        DB::beginTransaction();

        $order = $this->createOrder($request);
        $this->createOrderItems($order, $request->addons);
        $order->calculateTotals();

        $paymentGateway = $this->paymentManager->driver();
        $paymentData = [
            'payment_nonce' => $request->payment_nonce,
            'payment_method_id' => $request->payment_method_id,
        ];

        $paymentResult = $paymentGateway->createPayment($order, $paymentData);

        if ($paymentResult['success']) {
            $order->update([
                'payment_transaction_id' => $paymentResult['transaction_id'] ?? null,
                'payment_response' => $paymentResult['response'] ?? null,
            ]);

            DB::commit();

            // Handle PayUnit redirect
            if (config('payment.default_gateway') === Order::PAYMENT_GATEWAY_PAYUNIT && 
                isset($paymentResult['hosted_url'])) {
                return redirect($paymentResult['hosted_url']);
            }

            // Handle PayPal redirect
            if (config('payment.default_gateway') === Order::PAYMENT_GATEWAY_PAYPAL && 
                isset($paymentResult['approval_url'])) {
                return redirect($paymentResult['approval_url']);
            }

            // For Braintree/Stripe, payment is already captured
            $order->markAsPaid(
                $paymentResult['transaction_id'] ?? null,
                $paymentResult['response'] ?? null
            );

            $this->sendOrderEmails($order);

            return redirect()
                ->route('checkout.success', $order->id)
                ->with('success', __('messages.payment_success'));
        }

        // ... error handling ...
    }
}
```

Modify `success()` method to handle PayUnit return:

```php
public function success(Order $order)
{
    // If payment is already paid, just show success page
    if ($order->payment_status === Order::PAYMENT_STATUS_PAID) {
        return view('web.services.checkout-success', compact('order'));
    }

    // For PayUnit, verify payment via webhook data (payment already captured)
    if ($order->payment_gateway === Order::PAYMENT_GATEWAY_PAYUNIT && 
        $order->payment_status === Order::PAYMENT_STATUS_PENDING) {
        // Payment will be confirmed via webhook
        // Show pending message or wait for webhook
        return view('web.services.checkout-pending', compact('order'))
            ->with('info', __('messages.payment_processing'));
    }

    // For PayPal, capture the payment when customer returns from approval
    if ($order->payment_gateway === Order::PAYMENT_GATEWAY_PAYPAL && 
        $order->payment_status === Order::PAYMENT_STATUS_PENDING) {
        // ... existing PayPal capture logic ...
    }

    // Order not paid and not pending - redirect to checkout
    return redirect()->route('checkout.index');
}
```

#### Step 1.7: Create Webhook Controller

**File:** `app/Http/Controllers/Web/WebhookController.php`

Add PayUnit webhook handler:

```php
public function payunit(Request $request)
{
    try {
        $gateway = app(PayUnitGateway::class);
        
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
                Mail::to(config('mail.admin_email'))->send(new NewOrderNotification($order));
                
                Log::info('PayUnit payment successful', ['order_id' => $order->id]);
                break;

            case 'FAILED':
            case 'CANCELLED':
                $order->markAsFailed($payload);
                Log::warning('PayUnit payment failed/cancelled', ['order_id' => $order->id]);
                break;

            default:
                Log::warning('PayUnit unknown status', ['status' => $status]);
        }

        return response()->json(['success' => true], 200);

    } catch (\Exception $e) {
        Log::error('PayUnit webhook processing failed: ' . $e->getMessage());
        return response()->json(['error' => 'Processing failed'], 500);
    }
}
```

#### Step 1.8: Add Webhook Route

**File:** `routes/web.php`

```php
Route::post('/webhooks/payunit', [WebhookController::class, 'payunit'])->name('webhooks.payunit');
```

**Important:** Exclude from CSRF protection in `app/Http/Middleware/VerifyCsrfToken.php`:

```php
protected $except = [
    'webhooks/*',
];
```

---

### Phase 2: Frontend Integration ⏳

#### Step 2.1: Update Checkout View

**File:** `resources/views/web/services/checkout.blade.php`

PayUnit uses a hosted payment page, so no custom UI is needed. Just show a message:

```blade
@if($paymentGateway === 'payunit')
    <!-- PayUnit Payment Message -->
    <div id="payunit-message" class="mb-4">
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            {{ __('checkout.payunit_redirect_message') }}
        </div>
    </div>
@endif
```

#### Step 2.2: Update Form Submission

The form submission remains the same - no special JavaScript needed for PayUnit since it's redirect-based.

#### Step 2.3: Add Translations

**File:** `lang/en/checkout.php`

```php
'payunit_redirect_message' => 'You will be redirected to PayUnit to complete your payment securely.',
```

**File:** `lang/fr/checkout.php`

```php
'payunit_redirect_message' => 'Vous serez redirigé vers PayUnit pour finaliser votre paiement en toute sécurité.',
```

---

### Phase 3: Configuration & Environment ⏳

#### Step 3.1: Environment Variables

Add to `.env`:

```env
# PayUnit Configuration
PAYUNIT_MODE=sandbox
PAYUNIT_API_KEY=your_sandbox_api_key_here
PAYUNIT_API_USERNAME=your_api_username_here
PAYUNIT_API_PASSWORD=your_api_password_here
```

#### Step 3.2: Get PayUnit Credentials

1. Create account at https://pu.payunit.net/dashboard/register
2. Create a "PAYMENT COLLECTION" application
3. Activate the application
4. Get credentials from:
   - **API Credentials:** Settings → API CREDENTIALS tab
   - **Application Key:** Application Settings → APPLICATION DETAIL tab

---

### Phase 4: Testing ⏳

#### Step 4.1: Sandbox Testing

**Test Cards:**
- Visa: `4242 4224 2424 2424`
- Mastercard: `2223 0000 4840 0011`

**Test PayPal:**
- Email: `sb-hf17g4673731@business.example.com`
- Password: `ehQ5_)dA`

#### Step 4.2: Test Flow

1. Set `PAYMENT_GATEWAY=payunit` in `.env`
2. Access checkout page
3. Select service and add-ons
4. Fill customer information
5. Submit payment
6. Verify redirect to PayUnit hosted page
7. Complete payment on PayUnit
8. Verify redirect back to success page
9. Check webhook was received and processed
10. Verify order status updated to "paid"

#### Step 4.3: Webhook Testing

Use tools like:
- **Webhook.site** - Test webhook endpoint
- **ngrok** - Expose local server for webhook testing

---

### Phase 5: Documentation ⏳

#### Step 5.1: Update services.md

Add PayUnit section to payment gateways documentation.

#### Step 5.2: Create PayUnit Integration Guide

Create `documentation/payunit-integration-guide.md` with:
- Setup instructions
- Configuration guide
- Testing procedures
- Troubleshooting tips

---

## 4. Implementation Checklist

### Backend
- [ ] Install PayUnit PHP SDK via Composer
- [ ] Create `PayUnitGateway` class implementing `PaymentGatewayInterface`
- [ ] Add PayUnit configuration to `config/payment.php`
- [ ] Update `Order` model with PayUnit constant
- [ ] Update `PaymentGatewayManager` to support PayUnit
- [ ] Modify `CheckoutController::process()` for PayUnit redirect
- [ ] Modify `CheckoutController::success()` for PayUnit return
- [ ] Create `WebhookController::payunit()` method
- [ ] Add webhook route and CSRF exception
- [ ] Add environment variables to `.env.example`

### Frontend
- [ ] Add PayUnit message to checkout view
- [ ] Add PayUnit translations (EN/FR)
- [ ] Test form submission with PayUnit

### Testing
- [ ] Test payment initialization
- [ ] Test redirect to PayUnit hosted page
- [ ] Test successful payment flow
- [ ] Test failed payment flow
- [ ] Test cancelled payment flow
- [ ] Test webhook processing
- [ ] Test email notifications
- [ ] Test order status updates

### Documentation
- [ ] Update `services.md` with PayUnit info
- [ ] Create `payunit-integration-guide.md`
- [ ] Document environment variables
- [ ] Document testing procedures

---

## 5. Key Differences from Other Gateways

### 5.1 Hosted Page Model

Unlike Stripe (Elements) or PayPal (Buttons), PayUnit uses a **hosted payment page**:

- **Pros:**
  - PCI compliance handled by PayUnit
  - No frontend JavaScript integration needed
  - Supports multiple African payment methods
  - Mobile-optimized interface

- **Cons:**
  - Less control over UI/UX
  - Requires redirect (not inline)
  - Depends on webhook for confirmation

### 5.2 Payment Flow Comparison

| Gateway | Flow Type | UI Location | Capture |
|---------|-----------|-------------|---------|
| Stripe | Direct API | Merchant site | Immediate |
| PayPal | Hybrid | Both | Deferred |
| PayUnit | Redirect | PayUnit site | Immediate |

### 5.3 Webhook Importance

For PayUnit, **webhooks are critical** because:
- Payment confirmation happens asynchronously
- Customer may close browser before redirect
- Webhook is the source of truth for payment status

---

## 6. Production Considerations

### 6.1 Webhook Reliability

- Implement webhook retry logic
- Store webhook payloads for debugging
- Monitor webhook failures
- Have fallback for manual verification

### 6.2 Error Handling

- Handle API timeouts gracefully
- Provide clear error messages to customers
- Log all PayUnit API interactions
- Implement retry mechanism for failed requests

### 6.3 Security

- Validate webhook source (IP whitelist if available)
- Verify webhook payload integrity
- Use HTTPS for all URLs (return_url, notify_url)
- Store API credentials securely (never in code)

### 6.4 Monitoring

- Track payment success/failure rates
- Monitor webhook delivery
- Alert on payment anomalies
- Log all PayUnit transactions

---

## 7. Estimated Timeline

| Phase | Tasks | Estimated Time |
|-------|-------|----------------|
| **Phase 1** | Backend implementation | 4-6 hours |
| **Phase 2** | Frontend integration | 1-2 hours |
| **Phase 3** | Configuration & setup | 1 hour |
| **Phase 4** | Testing | 2-3 hours |
| **Phase 5** | Documentation | 1-2 hours |
| **Total** | | **9-14 hours** |

---

## 8. Next Steps

1. **Review this plan** with the team
2. **Create PayUnit account** and get credentials
3. **Install PayUnit SDK** and test basic API calls
4. **Implement backend** following Phase 1
5. **Test in sandbox** thoroughly
6. **Deploy to staging** for QA testing
7. **Go live** with production credentials

---

## 9. Support & Resources

### Official Documentation
- Developer Docs: https://developer.payunit.net/
- PHP SDK: https://developer.payunit.net/sdk-and-plugins/php
- REST API: https://developer.payunit.net/rest-api/overview

### Support Channels
- Email: info@payunit.net
- Website: https://payunit.net/

### Useful Links
- Swagger Documentation: https://gitlab.com/sevencommonfactor/payunit-documentation.git
- Test Environment: https://developer.payunit.net/getting-started

---

**Document Version:** 1.0  
**Last Updated:** December 29, 2025  
**Status:** Ready for Implementation
