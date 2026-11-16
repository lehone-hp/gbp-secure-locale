<?php

namespace App\Services;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Order;
use App\Services\PaymentGateways\BraintreeGateway;
use App\Services\PaymentGateways\MollieGateway;
use App\Services\PaymentGateways\StripeGateway;
use App\Services\PaymentGateways\PayPalGateway;
use App\Services\PaymentGateways\PayUnitGateway;
use App\Services\PaymentGateways\SquareGateway;
use App\Services\PaymentGateways\WhopGateway;
use InvalidArgumentException;

class PaymentGatewayManager
{
    protected TenantResolver $tenantResolver;

    public function __construct(TenantResolver $tenantResolver)
    {
        $this->tenantResolver = $tenantResolver;
    }

    public function driver(?string $gateway = null): PaymentGatewayInterface
    {
        $gateway = $gateway ?? $this->resolveGateway();

        return match($gateway) {
            Order::PAYMENT_GATEWAY_BRAINTREE => app(BraintreeGateway::class),
            Order::PAYMENT_GATEWAY_STRIPE => app(StripeGateway::class),
            Order::PAYMENT_GATEWAY_PAYPAL, Order::PAYMENT_GATEWAY_PAYPAL2 => app(PayPalGateway::class),
            Order::PAYMENT_GATEWAY_PAYUNIT => app(PayUnitGateway::class),
            Order::PAYMENT_GATEWAY_SQUARE => app(SquareGateway::class),
            Order::PAYMENT_GATEWAY_WHOP => app(WhopGateway::class),
            Order::PAYMENT_GATEWAY_MOLLIE => app(MollieGateway::class),
            default => throw new InvalidArgumentException("Payment gateway [{$gateway}] is not supported.")
        };
    }

    public function driverForTenant(string $domain): PaymentGatewayInterface
    {
        $this->tenantResolver->resolve($domain);
        return $this->driver();
    }

    protected function resolveGateway(): string
    {
        if ($this->tenantResolver->shouldUseTenantConfig()) {
            return $this->tenantResolver->getPaymentGateway();
        }

        return config('payment.default_gateway');
    }

    public function getAvailableGateways(): array
    {
        return array_keys(config('payment.gateways'));
    }

    public function getDefaultGateway(): string
    {
        return $this->resolveGateway();
    }

    public function getCurrentGateway(): string
    {
        return $this->resolveGateway();
    }

    /**
     * Whether the gateway serving this request can charge in $currency.
     */
    public function supportsCurrency(string $currency): bool
    {
        // PayPal account 2 shares the PayPal gateway configuration
        $gateway = $this->getCurrentGateway() === Order::PAYMENT_GATEWAY_PAYPAL2
            ? Order::PAYMENT_GATEWAY_PAYPAL
            : $this->getCurrentGateway();

        return in_array($currency, config("payment.gateways.{$gateway}.currencies", [config('currency.base')]), true);
    }

    /**
     * The visitor's selected currency when the gateway supports it, otherwise the base currency.
     */
    public function chargeCurrency(string $selected): string
    {
        return $this->supportsCurrency($selected) ? $selected : config('currency.base');
    }

    public function isTenantMode(): bool
    {
        return $this->tenantResolver->shouldUseTenantConfig();
    }
}
