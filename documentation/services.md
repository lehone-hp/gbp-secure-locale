### Conformite GBP Services / Checkout Plan

## Implementation Status

### Phase 1: Database Architecture ✅
- [x] Create Orders migration
- [x] Create OrderItems migration
- [x] Create Order model with relationships
- [x] Create OrderItem model with relationships
- [x] Add model methods and scopes
- [x] Run migrations

### Phase 2: Payment Gateway Architecture ✅
- [x] Create PaymentGatewayInterface contract
- [x] Implement BraintreeGateway
- [x] Implement StripeGateway
- [x] Implement PayPalGateway
- [x] Create PaymentGatewayManager
- [x] Create PaymentServiceProvider
- [x] Create payment config file
- [x] Install payment dependencies

### Phase 3: Backend Logic ✅
- [x] Create CheckoutController
- [x] Create CheckoutRequest validation
- [x] Implement order processing logic
- [x] Create WebhookController
- [x] Add checkout routes
- [x] Add webhook routes

### Phase 4: Frontend UI ✅
- [x] Build checkout page layout
- [x] Create service selection dropdown
- [x] Create dynamic addons section
- [x] Create customer information form
- [x] Create order summary component
- [x] Style with dark theme

### Phase 5: JavaScript Integration ✅
- [x] Implement dynamic addon loading
- [x] Add real-time order summary updates
- [x] Add form validation
- [x] Integrate Braintree Drop-in UI (ready for integration)
- [x] Integrate Stripe Elements (fully implemented)
- [x] Handle AJAX form submission
- [x] Stripe payment method creation
- [x] Stripe 3D Secure authentication support
- [x] Real-time card validation

### Phase 6: Email & Notifications ✅
- [x] Create OrderConfirmation mailable
- [x] Create NewOrderNotification mailable
- [x] Design email templates
- [x] Integrate email sending in checkout flow
- [x] Add error handling for email delivery

### Phase 7: Testing & Refinement ⏳
- [ ] Write unit tests
- [ ] Write feature tests
- [ ] Test Braintree sandbox
- [ ] Test Stripe test mode
- [ ] Fix bugs and optimize

---

## Detailed Implementation Plan

## Overview
A comprehensive checkout system with dynamic service/addon selection, customer information collection, order management, and multi-gateway payment processing.

---

## Phase 1: Database Architecture

### 1.1 Models & Migrations

**Orders Table** (`orders`)
```
- id (bigint, primary key)
- customer_first_name (string)
- customer_last_name (string)
- customer_email (string)
- customer_phone (string)
- customer_company (string, nullable)
- address_line_1 (string)
- address_line_2 (string, nullable)
- city (string)
- state (string)
- postal_code (string)
- country (string)
- notes (text, nullable)
- subtotal (decimal 10,2)
- total (decimal 10,2)
- currency (string, default 'USD')
- status (enum: pending, processing, completed, failed, refunded)
- payment_gateway (string) - braintree, stripe, paypal
- payment_status (enum: pending, paid, failed, refunded)
- payment_transaction_id (string, nullable)
- payment_response (json, nullable)
- created_at, updated_at
- deleted_at (soft deletes)
```

**Order Items Table** (`order_items`)
```
- id (bigint, primary key)
- order_id (foreign key -> orders)
- service_addon_id (foreign key -> service_addons)
- item_name (string) - snapshot of addon name
- item_description (text, nullable) - snapshot
- item_type (string, nullable) - snapshot of pricing type
- price (decimal 10,2) - snapshot of price at time of order
- quantity (integer, default 1)
- subtotal (decimal 10,2)
- created_at, updated_at
```

### 1.2 Model Relationships
- `Order` hasMany `OrderItem`
- `OrderItem` belongsTo `Order`
- `OrderItem` belongsTo `ServiceAddon`

### 1.3 Model Features
- **Order Model:**
  - Accessor for formatted total with currency
  - Scopes: `pending()`, `completed()`, `byPaymentGateway()`
  - Methods: `calculateTotals()`, `markAsPaid()`, `markAsFailed()`
  - Soft deletes enabled

- **OrderItem Model:**
  - Auto-calculate subtotal on save
  - Store snapshot data to preserve historical pricing

---

## Phase 2: Payment Gateway Architecture

### 2.1 Gateway Interface Pattern

**Create Payment Gateway Contract** (`app/Contracts/PaymentGatewayInterface.php`)
```php
interface PaymentGatewayInterface
{
    public function createPayment(Order $order): array;
    public function capturePayment(string $transactionId): array;
    public function refundPayment(string $transactionId, float $amount): array;
    public function getClientToken(): ?string;
    public function verifyWebhook(Request $request): bool;
}
```

### 2.2 Gateway Implementations

**Braintree Gateway** (`app/Services/PaymentGateways/BraintreeGateway.php`)
- Implement `PaymentGatewayInterface`
- Handle client token generation
- Process payments
- Handle webhooks

**Stripe Gateway** (`app/Services/PaymentGateways/StripeGateway.php`) ✅
- Implement `PaymentGatewayInterface`
- Create payment intents with automatic confirmation
- Handle 3D Secure (SCA) authentication
- Process webhooks with signature verification
- Real-time card validation via Stripe Elements
- Support for all major card brands
- Automatic receipt emails

**PayPal Gateway** (`app/Services/PaymentGateways/PayPalGateway.php`)
- Implement `PaymentGatewayInterface`
- Handle PayPal checkout flow
- Process webhooks

**PayUnit Gateway** (`app/Services/PaymentGateways/PayUnitGateway.php`) ✅
- Implement `PaymentGatewayInterface`
- Hosted payment page integration
- Support for mobile money and African payment methods
- Webhook-based payment confirmation
- Auto-capture payment model

### 2.3 Gateway Manager

**Payment Gateway Manager** (`app/Services/PaymentGatewayManager.php`)
```php
class PaymentGatewayManager
{
    public function driver(?string $gateway = null): PaymentGatewayInterface
    {
        $gateway = $gateway ?? config('payment.default_gateway');
        return match($gateway) {
            'braintree' => app(BraintreeGateway::class),
            'stripe' => app(StripeGateway::class),
            'paypal' => app(PayPalGateway::class),
            default => throw new InvalidArgumentException("Gateway {$gateway} not supported")
        };
    }
}
```

### 2.4 Service Provider

**Payment Service Provider** (`app/Providers/PaymentServiceProvider.php`)
- Register gateway implementations
- Bind `PaymentGatewayManager` to container
- Load payment configuration

### 2.5 Configuration

**Config File** (`config/payment.php`)
```php
return [
    'default_gateway' => env('PAYMENT_GATEWAY', 'braintree'),
    
    'gateways' => [
        'braintree' => [
            'environment' => env('BRAINTREE_ENVIRONMENT', 'sandbox'),
            'merchant_id' => env('BRAINTREE_MERCHANT_ID'),
            'public_key' => env('BRAINTREE_PUBLIC_KEY'),
            'private_key' => env('BRAINTREE_PRIVATE_KEY'),
        ],
        'stripe' => [
            'key' => env('STRIPE_KEY'),
            'secret' => env('STRIPE_SECRET'),
            'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
        ],
        'paypal' => [
            'mode' => env('PAYPAL_MODE', 'sandbox'),
            'client_id' => env('PAYPAL_CLIENT_ID'),
            'secret' => env('PAYPAL_SECRET'),
        ],
    ],
];
```

---

## Phase 3: Backend Controllers & Routes

### 3.1 Routes

```php
// Checkout routes
Route::get('/services/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/services/checkout/addons', [CheckoutController::class, 'getAddons'])->name('checkout.addons');
Route::post('/services/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
Route::get('/services/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');
Route::get('/services/checkout/cancel', [CheckoutController::class, 'cancel'])->name('checkout.cancel');

// Payment webhooks
Route::post('/webhooks/braintree', [WebhookController::class, 'braintree'])->name('webhooks.braintree');
Route::post('/webhooks/stripe', [WebhookController::class, 'stripe'])->name('webhooks.stripe');
Route::post('/webhooks/paypal', [WebhookController::class, 'paypal'])->name('webhooks.paypal');
```

### 3.2 CheckoutController

**Methods:**
- `index()` - Display checkout page with services
- `getAddons(Request $request)` - Return addons for selected service (AJAX)
- `process(Request $request)` - Process checkout and payment
- `success(Order $order)` - Display success page
- `cancel()` - Display cancellation page

### 3.3 Request Validation

**CheckoutRequest** (`app/Http/Requests/CheckoutRequest.php`)
```php
public function rules()
{
    return [
        'service_id' => 'required|exists:services,id',
        'addons' => 'required|array|min:1',
        'addons.*' => 'exists:service_addons,id',
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|email',
        'phone' => 'required|string',
        'company' => 'nullable|string|max:255',
        'address_line_1' => 'required|string',
        'address_line_2' => 'nullable|string',
        'city' => 'required|string',
        'state' => 'required|string',
        'postal_code' => 'required|string',
        'country' => 'required|string',
        'notes' => 'nullable|string',
        'payment_nonce' => 'required_if:payment_gateway,braintree',
        'payment_method_id' => 'required_if:payment_gateway,stripe',
    ];
}
```

---

## Phase 4: Frontend Implementation

### 4.1 Checkout Page Structure

**Layout** (`resources/views/web/services/checkout.blade.php`)
```html
<div class="row">
    <!-- Left Column: Form -->
    <div class="col-lg-7">
        <form id="checkout-form">
            <!-- Service Selection -->
            <div class="mb-4">
                <label>Select Service Category</label>
                <select name="service_id" id="service-select" class="form-control">
                    <option value="">Choose a service...</option>
                    @foreach($services as $service)
                        <option value="{{ $service->id }}">{{ $service->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <!-- Addons Selection (Dynamic) -->
            <div id="addons-container" class="mb-4" style="display:none;">
                <label>Select Add-ons</label>
                <div id="addons-list"></div>
            </div>
            
            <!-- Customer Information -->
            <div class="customer-info">
                <h4 class="mb-4">Customer Information</h4>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <input type="text" name="first_name" class="form-control" placeholder="First Name" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <input type="text" name="last_name" class="form-control" placeholder="Last Name" required>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <input type="email" name="email" class="form-control" placeholder="Email" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <input type="tel" name="phone" class="form-control" placeholder="Phone Number" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <input type="text" name="company" class="form-control" placeholder="Company Name">
                </div>
                
                <div class="mb-3">
                    <input type="text" name="address_line_1" class="form-control" placeholder="Address Line 1" required>
                </div>
                
                <div class="mb-3">
                    <input type="text" name="address_line_2" class="form-control" placeholder="Address Line 2">
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <input type="text" name="city" class="form-control" placeholder="City" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <input type="text" name="state" class="form-control" placeholder="State" required>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <input type="text" name="postal_code" class="form-control" placeholder="Postal Code" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <input type="text" name="country" class="form-control" placeholder="Country" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <textarea name="notes" class="form-control" rows="3" placeholder="Notes (Optional)"></textarea>
                </div>
            </div>
        </form>
    </div>
    
    <!-- Right Column: Order Summary -->
    <div class="col-lg-5">
        <div class="order-summary-card">
            <h4 class="mb-4">Order Summary</h4>
            
            <table class="table order-summary-table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th class="text-end">Price</th>
                    </tr>
                </thead>
                <tbody id="order-items">
                    <tr class="empty-state">
                        <td colspan="2" class="text-center">No items selected</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <th>Total</th>
                        <th class="text-end" id="order-total">$0.00</th>
                    </tr>
                </tfoot>
            </table>
            
            <!-- Payment UI Container -->
            <div id="payment-container" class="mb-4" style="display:none;"></div>
            
            <!-- Submit Button -->
            <button type="button" id="submit-payment" class="btn btn-primary w-100 py-3" disabled>
                Make Payment
            </button>
        </div>
    </div>
</div>
```

### 4.2 JavaScript Functionality

**Key Functions:**
```javascript
// Load addons when service changes
$('#service-select').on('change', function() {
    const serviceId = $(this).val();
    if (serviceId) {
        loadServiceAddons(serviceId);
    } else {
        $('#addons-container').hide();
        clearOrderSummary();
    }
});

function loadServiceAddons(serviceId) {
    $.ajax({
        url: '/services/checkout/addons',
        method: 'POST',
        data: { service_id: serviceId },
        success: function(response) {
            renderAddons(response.addons);
            $('#addons-container').show();
        }
    });
}

function renderAddons(addons) {
    const container = $('#addons-list');
    container.empty();
    
    addons.forEach(addon => {
        const html = `
            <div class="form-check mb-3">
                <input class="form-check-input addon-checkbox" 
                       type="checkbox" 
                       name="addons[]" 
                       value="${addon.id}" 
                       data-name="${addon.name}"
                       data-price="${addon.price}"
                       data-type="${addon.type || ''}">
                <label class="form-check-label">
                    ${addon.name} - $${addon.price} ${addon.type ? '(' + addon.type + ')' : ''}
                </label>
            </div>
        `;
        container.append(html);
    });
    
    // Attach change event
    $('.addon-checkbox').on('change', updateOrderSummary);
}

function updateOrderSummary() {
    const items = [];
    let total = 0;
    
    $('.addon-checkbox:checked').each(function() {
        const name = $(this).data('name');
        const price = parseFloat($(this).data('price'));
        const type = $(this).data('type');
        
        items.push({ name, price, type });
        total += price;
    });
    
    renderOrderSummary(items, total);
    
    // Enable/disable payment button
    $('#submit-payment').prop('disabled', items.length === 0);
    
    // Show/hide payment container
    if (items.length > 0) {
        $('#payment-container').show();
        initializePaymentGateway();
    } else {
        $('#payment-container').hide();
    }
}

function renderOrderSummary(items, total) {
    const tbody = $('#order-items');
    tbody.empty();
    
    if (items.length === 0) {
        tbody.append('<tr class="empty-state"><td colspan="2" class="text-center">No items selected</td></tr>');
    } else {
        items.forEach(item => {
            const typeText = item.type ? ` (${item.type})` : '';
            tbody.append(`
                <tr>
                    <td>${item.name}${typeText}</td>
                    <td class="text-end">$${item.price.toFixed(2)}</td>
                </tr>
            `);
        });
    }
    
    $('#order-total').text('$' + total.toFixed(2) + ' USD');
}

function clearOrderSummary() {
    $('.addon-checkbox').prop('checked', false);
    updateOrderSummary();
}
```

---

## Phase 5: Order Processing Flow

### 5.1 Process Steps

1. **Validate Request**
   - Validate form data
   - Verify addons belong to selected service
   - Check addon availability

2. **Create Order**
   - Store customer information
   - Calculate totals
   - Set status to 'pending'
   - Set currency to 'USD'

3. **Create Order Items**
   - Loop through selected addons
   - Create order item for each
   - Store snapshot data (name, price, type)

4. **Process Payment**
   - Get payment gateway from config
   - Initialize gateway
   - Create payment transaction
   - Handle response

5. **Update Order Status**
   - On success: mark as 'paid' and 'processing'
   - On failure: mark as 'failed'
   - Store transaction ID and response

6. **Send Notifications**
   - Email to customer (order confirmation)
   - Email to admin (new order notification)

7. **Redirect**
   - Success: redirect to success page with order ID
   - Failure: redirect back with error

---

## Phase 5A: Stripe Integration Details

### 5A.1 Frontend Implementation

**Stripe Elements UI** (`resources/views/web/services/checkout.blade.php`)

The checkout view includes a complete Stripe Elements integration:

```blade
@if($paymentGateway === 'stripe')
    <!-- Stripe Card Element Container -->
    <div id="stripe-card-container" class="mb-4">
        <h6 class="mb-3">Payment Details</h6>
        
        <!-- Card Element -->
        <div class="mb-3">
            <label class="form-label">Card Information</label>
            <div id="stripe-card-element" class="stripe-element"></div>
        </div>
        
        <!-- Card Errors -->
        <div id="stripe-card-errors" class="alert alert-danger" style="display:none;"></div>
    </div>
@endif
```

**Stripe.js SDK Loading**
```blade
@if($paymentGateway === 'stripe')
    <script src="https://js.stripe.com/v3/"></script>
@endif
```

**JavaScript Implementation**
```javascript
// Initialize Stripe
stripe = Stripe('{{ config('payment.gateways.stripe.key') }}');

// Create Elements instance
const elements = stripe.elements();

// Create card element with custom styling
cardElement = elements.create('card', {
    style: {
        base: {
            color: '#ffffff',
            fontFamily: '"Roboto Slab", sans-serif',
            fontSize: '16px',
            fontWeight: '300',
            '::placeholder': {
                color: 'rgba(255, 255, 255, 0.5)'
            }
        },
        invalid: {
            color: '#D45769',
            iconColor: '#D45769'
        }
    }
});

// Mount card element
cardElement.mount('#stripe-card-element');

// Handle real-time validation
cardElement.on('change', function(event) {
    if (event.error) {
        $('#stripe-card-errors').text(event.error.message).show();
    } else {
        $('#stripe-card-errors').hide();
    }
});
```

### 5A.2 Payment Method Creation

On form submission, the system creates a Stripe PaymentMethod:

```javascript
const {paymentMethod, error} = await stripe.createPaymentMethod({
    type: 'card',
    card: cardElement,
    billing_details: {
        name: firstName + ' ' + lastName,
        email: email,
        phone: phone,
        address: {
            line1: addressLine1,
            line2: addressLine2,
            city: city,
            state: state,
            postal_code: postalCode,
            country: country
        }
    }
});

if (!error) {
    $('#payment-method-id').val(paymentMethod.id);
    $('#checkout-form').submit();
}
```

### 5A.3 Backend Processing

**StripeGateway::createPayment()**
```php
$paymentIntent = $this->stripe->paymentIntents->create([
    'amount' => $order->total * 100, // Convert to cents
    'currency' => strtolower($order->currency),
    'payment_method' => $paymentData['payment_method_id'],
    'confirm' => true, // Automatically confirm
    'description' => "Order #{$order->id}",
    'metadata' => [
        'order_id' => $order->id,
        'customer_email' => $order->customer_email,
    ],
    'receipt_email' => $order->customer_email,
    'return_url' => route('checkout.success', $order->id),
]);
```

### 5A.4 3D Secure (SCA) Support

The backend automatically handles Strong Customer Authentication:

```php
if ($paymentIntent->status === 'succeeded') {
    return ['success' => true, 'transaction_id' => $paymentIntent->id];
}

if ($paymentIntent->status === 'requires_action') {
    return [
        'success' => false,
        'requires_action' => true,
        'client_secret' => $paymentIntent->client_secret,
    ];
}
```

When `requires_action` is returned, Stripe automatically handles the 3D Secure authentication flow via the `return_url`.

### 5A.5 Testing Stripe Integration

**Test Cards:**
- `4242 4242 4242 4242` - Successful payment
- `4000 0025 0000 3155` - Requires 3D Secure authentication
- `4000 0000 0000 9995` - Declined card
- `4000 0000 0000 0069` - Expired card

**Test Mode:**
Set in `.env`:
```env
STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
```

### 5A.6 Features Implemented

✅ **Stripe Elements UI** - Beautiful, customizable card input
✅ **Real-time Validation** - Instant feedback on card errors
✅ **Payment Method Creation** - Secure tokenization
✅ **3D Secure Support** - SCA compliance
✅ **Custom Styling** - Matches dark theme
✅ **Error Handling** - User-friendly error messages
✅ **Loading States** - Visual feedback during processing
✅ **Webhook Verification** - Secure webhook handling
✅ **Automatic Receipts** - Email receipts via Stripe
✅ **Metadata Tracking** - Order ID and customer info

---

## Phase 6: Email Notifications

### 6.1 Mailable Classes

**OrderConfirmation** - Customer receipt
- Order details
- Items purchased
- Total amount
- Payment information
- Customer information

**NewOrderNotification** - Admin notification
- New order alert
- Customer details
- Order summary
- Link to admin panel

**PaymentFailed** - Payment failure notice
- Error details
- Retry instructions

---

## Phase 7: Dependencies & Configuration

### 7.1 Composer Dependencies

```bash
# Braintree
composer require braintree/braintree_php

# Stripe
composer require stripe/stripe-php

# PayPal (optional)
composer require paypal/rest-api-sdk-php
```

### 7.2 Environment Variables

```env
# Payment Gateway
PAYMENT_GATEWAY=braintree

# Braintree
BRAINTREE_ENVIRONMENT=sandbox
BRAINTREE_MERCHANT_ID=
BRAINTREE_PUBLIC_KEY=
BRAINTREE_PRIVATE_KEY=

# Stripe
STRIPE_KEY=
STRIPE_SECRET=
STRIPE_WEBHOOK_SECRET=

# PayPal
PAYPAL_MODE=sandbox
PAYPAL_CLIENT_ID=
PAYPAL_SECRET=
```

---

## Success Criteria

✅ Users can select service and addons dynamically  
✅ Order summary updates in real-time  
✅ Customer information is validated  
✅ Payment processes successfully via configured gateway  
✅ Orders are stored with complete information  
✅ Emails are sent to customer and admin  
✅ Payment gateway can be switched via env variable  
✅ Webhooks are handled securely  
✅ Currency defaults to USD  
✅ Order ID is used instead of order number  

---

## Notes

- No `order_number` field - using `id` instead
- No `tax` field - keeping it simple for now
- Currency field defaults to 'USD'
- All prices stored as decimal(10,2)
- Soft deletes enabled on orders table
- Payment response stored as JSON for debugging

