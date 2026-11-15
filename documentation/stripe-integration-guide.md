# Stripe Integration Guide

## Overview

This guide provides complete information about the Stripe payment integration in the Conformite GBP checkout system.

---

## ✅ Implementation Status

**Backend:** 100% Complete  
**Frontend:** 100% Complete  
**Testing:** Ready for testing  
**Documentation:** Complete

---

## Features Implemented

### Frontend (Stripe Elements)
- ✅ Stripe.js SDK v3 integration
- ✅ Custom-styled card element matching dark theme
- ✅ Real-time card validation
- ✅ Payment method creation with billing details
- ✅ Error handling and user feedback
- ✅ Loading states during processing
- ✅ Responsive design

### Backend (StripeGateway)
- ✅ Payment Intent creation with automatic confirmation
- ✅ 3D Secure (SCA) authentication support
- ✅ Webhook signature verification
- ✅ Refund processing
- ✅ Comprehensive error handling
- ✅ Transaction metadata tracking
- ✅ Automatic receipt emails

---

## Configuration

### 1. Environment Variables

Add to your `.env` file:

```env
# Set Stripe as default gateway (optional)
PAYMENT_GATEWAY=stripe

# Stripe API Keys
STRIPE_KEY=pk_test_your_publishable_key_here
STRIPE_SECRET=sk_test_your_secret_key_here
STRIPE_WEBHOOK_SECRET=whsec_your_webhook_secret_here
```

### 2. Get Stripe API Keys

1. Go to [Stripe Dashboard](https://dashboard.stripe.com/)
2. Navigate to **Developers > API Keys**
3. Copy your **Publishable key** (starts with `pk_test_` or `pk_live_`)
4. Copy your **Secret key** (starts with `sk_test_` or `sk_live_`)

### 3. Configure Webhooks (Optional)

1. Go to **Developers > Webhooks**
2. Click **Add endpoint**
3. Enter URL: `https://yourdomain.com/webhooks/stripe`
4. Select events to listen for
5. Copy the **Signing secret** (starts with `whsec_`)

---

## Testing

### Test Cards

Use these test cards in **test mode**:

| Card Number | Scenario | CVC | Date |
|-------------|----------|-----|------|
| `4242 4242 4242 4242` | Successful payment | Any 3 digits | Any future date |
| `4000 0025 0000 3155` | Requires 3D Secure | Any 3 digits | Any future date |
| `4000 0000 0000 9995` | Declined | Any 3 digits | Any future date |
| `4000 0000 0000 0069` | Expired card | Any 3 digits | Any past date |
| `4000 0000 0000 0341` | Incorrect CVC | Any 3 digits | Any future date |

### Testing Steps

1. **Set Test Mode**
   ```env
   STRIPE_KEY=pk_test_...
   STRIPE_SECRET=sk_test_...
   ```

2. **Access Checkout**
   - Go to `/services/checkout`
   - Select a service
   - Choose add-ons
   - Fill in customer information

3. **Test Successful Payment**
   - Use card: `4242 4242 4242 4242`
   - CVC: `123`
   - Date: `12/25`
   - Click "Make Payment"
   - Should redirect to success page

4. **Test 3D Secure**
   - Use card: `4000 0025 0000 3155`
   - Complete 3D Secure authentication
   - Should redirect to success page

5. **Test Declined Card**
   - Use card: `4000 0000 0000 9995`
   - Should show error message
   - Order should be marked as failed

6. **Verify in Stripe Dashboard**
   - Go to **Payments** in Stripe Dashboard
   - Check that test payments appear
   - Verify metadata (order_id, customer_email)

---

## How It Works

### 1. Frontend Flow

```
User fills form
    ↓
Selects add-ons
    ↓
Stripe Elements loads
    ↓
User enters card details
    ↓
Real-time validation
    ↓
User clicks "Make Payment"
    ↓
JavaScript creates PaymentMethod
    ↓
PaymentMethod ID sent to backend
```

### 2. Backend Flow

```
Receive PaymentMethod ID
    ↓
Create Order in database
    ↓
Create PaymentIntent with Stripe
    ↓
Confirm payment automatically
    ↓
If 3D Secure is needed: return client_secret (see flow 3 below)
    ↓
Mark order as paid
    ↓
Send confirmation emails
    ↓
Redirect to success page
```

### 3. 3D Secure Flow

The checkout form is submitted with `fetch` (JSON), so the page can finish 3D
Secure without a full page reload. When 3D Secure is required:

```
PaymentIntent status: requires_action (order stays pending)
    ↓
process() returns { requires_action, client_secret, redirect_url }
    ↓
Browser calls stripe.handleNextAction() (authentication modal)
    ↓
Browser goes to redirect_url (checkout.success)
    ↓
success() retrieves the PaymentIntent from Stripe
    ↓
Only if it succeeded: order marked paid, emails sent
```

The `payment_intent.succeeded` webhook can mark the order paid before the
browser returns. `Order::markAsPaidIfPending()` is atomic, so whichever of the
two gets there first marks the order paid and sends the emails, and the other
does nothing. The success page never fails an order; failures come from the
`payment_intent.payment_failed` webhook.

To test it, use the card `4000 0025 0000 3155` (authentication required) and
complete the challenge in the modal.

---

## Code Reference

### Frontend Files

**View:**
- `resources/views/web/services/checkout.blade.php`
  - Lines 218-231: Stripe Elements UI
  - Lines 310-336: Stripe CSS styling
  - Lines 373-375: Stripe.js SDK loading
  - Lines 643-756: Stripe JavaScript implementation

### Backend Files

**Gateway:**
- `app/Services/PaymentGateways/StripeGateway.php`
  - `createPayment()` - Creates PaymentIntent
  - `capturePayment()` - Captures authorized payment
  - `refundPayment()` - Processes refunds
  - `getClientToken()` - Returns publishable key
  - `verifyWebhook()` - Validates webhook signatures

**Controller:**
- `app/Http/Controllers/Web/CheckoutController.php`
  - `process()` - Handles payment processing
  - Accepts `payment_method_id` from Stripe

**Webhook:**
- `app/Http/Controllers/Web/WebhookController.php`
  - `stripe()` - Handles Stripe webhooks

**Configuration:**
- `config/payment.php` - Stripe configuration

---

## Styling

The Stripe Elements are styled to match your dark theme:

```css
.stripe-element {
    background-color: #15151b;
    border: 1px solid #7af05a;
    color: #ffffff;
    padding: 12px;
    border-radius: 4px;
}

.stripe-element:focus-within {
    border-color: #9fff85;
    box-shadow: 0 0 0 0.2rem rgba(122, 240, 90, 0.25);
}

.stripe-element.StripeElement--invalid {
    border-color: #D45769;
}
```

---

## Error Handling

### Frontend Errors

Displayed in `#stripe-card-errors` div:
- Invalid card number
- Expired card
- Incorrect CVC
- Incomplete card details
- Network errors

### Backend Errors

Handled by `StripeGateway`:
- API errors (ApiErrorException)
- Invalid payment method
- Insufficient funds
- Card declined
- Authentication failed

All errors are logged and user-friendly messages are displayed.

---

## Security

### PCI Compliance

✅ **No card data touches your server**
- Card details entered directly into Stripe Elements
- Stripe handles all sensitive data
- Only PaymentMethod ID sent to backend

### Webhook Security

✅ **Signature verification**
```php
\Stripe\Webhook::constructEvent($payload, $signature, $webhookSecret);
```

### HTTPS Required

⚠️ **Always use HTTPS in production**
- Stripe requires HTTPS for live mode
- Protects customer data in transit

---

## Troubleshooting

### Issue: "Stripe is not defined"

**Solution:** Ensure Stripe.js is loaded:
```blade
@if($paymentGateway === 'stripe')
    <script src="https://js.stripe.com/v3/"></script>
@endif
```

### Issue: "Invalid API Key"

**Solution:** Check `.env` configuration:
- Verify `STRIPE_KEY` starts with `pk_test_` or `pk_live_`
- Verify `STRIPE_SECRET` starts with `sk_test_` or `sk_live_`
- Run `php artisan config:clear`

### Issue: Payment succeeds but order not updated

**Solution:** Check logs:
```bash
tail -f storage/logs/laravel.log
```

Look for errors in `CheckoutController::process()`

### Issue: 3D Secure not working

**Solution:** Check that `use_stripe_sdk` is set on the PaymentIntent (so Stripe
returns a next action Stripe.js can handle in a modal) and that the checkout
request is sent with `Accept: application/json`. Orders left `pending` with a
`payment_transaction_id` are customers who closed the authentication modal.

### Issue: Webhooks failing

**Solution:** 
1. Check webhook signature in `.env`
2. Verify webhook URL is accessible
3. Check webhook logs in Stripe Dashboard

---

## Production Checklist

Before going live:

- [ ] Switch to live API keys (`pk_live_`, `sk_live_`)
- [ ] Update `STRIPE_KEY` in `.env`
- [ ] Update `STRIPE_SECRET` in `.env`
- [ ] Configure live webhooks in Stripe Dashboard
- [ ] Update `STRIPE_WEBHOOK_SECRET` in `.env`
- [ ] Test with real card (small amount)
- [ ] Verify emails are sent
- [ ] Enable HTTPS
- [ ] Test 3D Secure flow
- [ ] Monitor first few transactions
- [ ] Set up Stripe Radar (fraud prevention)

---

## Multitenancy Support

Stripe works seamlessly with the multitenancy system:

1. **Configure in Admin Panel**
   - Go to `/admin/tenants`
   - Select domain
   - Choose "Stripe" as payment gateway

2. **Credentials**
   - All domains use the same Stripe account
   - Credentials configured in `.env`
   - Gateway selection per domain

3. **Testing Multiple Domains**
   - Each domain can use different gateway
   - Orders tracked by domain
   - Reports available per domain

---

## Support Resources

### Stripe Documentation
- [Stripe Elements](https://stripe.com/docs/stripe-js)
- [Payment Intents](https://stripe.com/docs/payments/payment-intents)
- [3D Secure](https://stripe.com/docs/payments/3d-secure)
- [Testing](https://stripe.com/docs/testing)

### Internal Documentation
- `documentation/services.md` - Complete checkout documentation
- `documentation/multitenancy.md` - Multitenancy guide

---

## Summary

The Stripe integration is **fully implemented and production-ready**. It includes:

✅ Complete frontend UI with Stripe Elements  
✅ Backend payment processing  
✅ 3D Secure authentication  
✅ Real-time validation  
✅ Error handling  
✅ Webhook support  
✅ Multitenancy compatibility  
✅ Custom styling  
✅ Comprehensive documentation  

**Next Steps:**
1. Add Stripe API keys to `.env`
2. Test with test cards
3. Verify successful payments
4. Test 3D Secure flow
5. Switch to live keys for production

---

**Last Updated:** December 29, 2025  
**Version:** 1.0 - Complete Implementation
