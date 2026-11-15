<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantConfig extends Model
{
    protected $fillable = [
        'domain',
        'payment_gateway',
        'is_active',
        'settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'array',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByDomain($query, string $domain)
    {
        return $query->where('domain', $domain);
    }

    public function isGatewayConfigured(): bool
    {
        return !empty($this->payment_gateway);
    }

    public static function findByDomain(string $domain): ?self
    {
        return static::byDomain($domain)->active()->first();
    }

    public function getGatewayName(): string
    {
        return match($this->payment_gateway) {
            Order::PAYMENT_GATEWAY_BRAINTREE => 'Braintree',
            Order::PAYMENT_GATEWAY_STRIPE => 'Stripe',
            Order::PAYMENT_GATEWAY_PAYPAL => 'PayPal',
            Order::PAYMENT_GATEWAY_PAYPAL2 => 'PayPal (Account 2)',
            default => ucfirst($this->payment_gateway),
        };
    }
}
