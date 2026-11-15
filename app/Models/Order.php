<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_REFUNDED = 'refunded';

    const PAYMENT_STATUS_PENDING = 'pending';
    const PAYMENT_STATUS_PAID = 'paid';
    const PAYMENT_STATUS_FAILED = 'failed';
    const PAYMENT_STATUS_REFUNDED = 'refunded';

    const PAYMENT_GATEWAY_BRAINTREE = 'braintree';
    const PAYMENT_GATEWAY_STRIPE = 'stripe';
    const PAYMENT_GATEWAY_PAYPAL = 'paypal';
    const PAYMENT_GATEWAY_PAYPAL2 = 'paypal2';
    const PAYMENT_GATEWAY_PAYUNIT = 'payunit';
    const PAYMENT_GATEWAY_SQUARE = 'square';
    const PAYMENT_GATEWAY_WHOP = 'whop';

    const PAYMENT_GATEWAY_LABELS = [
        self::PAYMENT_GATEWAY_BRAINTREE => 'Braintree',
        self::PAYMENT_GATEWAY_STRIPE => 'Stripe',
        self::PAYMENT_GATEWAY_PAYPAL => 'PayPal',
        self::PAYMENT_GATEWAY_PAYPAL2 => 'PayPal (Account 2)',
        self::PAYMENT_GATEWAY_PAYUNIT => 'PayUnit',
        self::PAYMENT_GATEWAY_SQUARE => 'Square',
        self::PAYMENT_GATEWAY_WHOP => 'Whop',
    ];

    const PAYMENT_GATEWAY_VALUES = [
        self::PAYMENT_GATEWAY_BRAINTREE,
        self::PAYMENT_GATEWAY_STRIPE,
        self::PAYMENT_GATEWAY_PAYPAL,
        self::PAYMENT_GATEWAY_PAYPAL2,
        self::PAYMENT_GATEWAY_PAYUNIT,
        self::PAYMENT_GATEWAY_SQUARE,
        self::PAYMENT_GATEWAY_WHOP,
    ];

    // Gateway types that both mean "PayPal" for UI/lookup purposes, differing
    // only in which credential set is active (see TenantResolver::resolve()).
    const PAYPAL_GATEWAY_VARIANTS = [
        self::PAYMENT_GATEWAY_PAYPAL,
        self::PAYMENT_GATEWAY_PAYPAL2,
    ];


    const CURRENCY_USD = 'USD';
    const CURRENCY_EUR = 'EUR';
    const CURRENCY_GBP = 'GBP';

    protected $fillable = [
        'customer_first_name',
        'customer_last_name',
        'customer_email',
        'customer_phone',
        'customer_company',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'postal_code',
        'country',
        'notes',
        'terms_accepted_at',
        'immediate_start_accepted',
        'policy_version',
        'subtotal',
        'total',
        'currency',
        'exchange_rate',
        'status',
        'payment_gateway',
        'domain',
        'payment_status',
        'payment_transaction_id',
        'payment_response',
    ];
    protected $casts = [
        'subtotal' => 'decimal:2',
        'total' => 'decimal:2',
        'payment_response' => 'array',
        'terms_accepted_at' => 'datetime',
        'immediate_start_accepted' => 'boolean',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getFormattedTotalAttribute()
    {
        return number_format($this->total, 2) . ' ' . $this->currency;
    }

    public function getCustomerFullNameAttribute()
    {
        return $this->customer_first_name . ' ' . $this->customer_last_name;
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', self::STATUS_PROCESSING);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    public function scopeByPaymentGateway($query, $gateway)
    {
        return $query->where('payment_gateway', $gateway);
    }

    public function calculateTotals()
    {
        $this->subtotal = $this->items->sum('subtotal');
        $this->total = $this->subtotal;
        $this->save();
    }

    public function markAsPaid($transactionId = null, $response = null)
    {
        $this->payment_status = self::PAYMENT_STATUS_PAID;
        $this->status = self::STATUS_PROCESSING;

        if ($transactionId) {
            $this->payment_transaction_id = $transactionId;
        }

        if ($response) {
            $this->payment_response = $response;
        }

        $this->save();
    }

    /**
     * Atomically mark a pending order as paid. Returns false when another
     * request (e.g. the webhook) already did, so the caller can skip side
     * effects such as sending emails.
     */
    public function markAsPaidIfPending($transactionId = null, $response = null): bool
    {
        $claimed = static::whereKey($this->id)
            ->where('payment_status', self::PAYMENT_STATUS_PENDING)
            ->update(['payment_status' => self::PAYMENT_STATUS_PAID]);

        if (! $claimed) {
            return false;
        }

        $this->markAsPaid($transactionId, $response);

        return true;
    }

    public function markAsFailed($response = null)
    {
        $this->payment_status = self::PAYMENT_STATUS_FAILED;
        $this->status = self::STATUS_FAILED;

        if ($response) {
            $this->payment_response = $response;
        }

        $this->save();
    }

    public function markAsCompleted()
    {
        $this->status = self::STATUS_COMPLETED;
        $this->save();
    }

    public function markAsRefunded($transactionId = null)
    {
        $this->payment_status = self::PAYMENT_STATUS_REFUNDED;
        $this->status = self::STATUS_REFUNDED;

        if ($transactionId) {
            $this->payment_transaction_id = $transactionId;
        }

        $this->save();
    }
}
