<?php

namespace App\Services;

use App\Models\Order;
use App\Models\TenantConfig;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class TenantResolver
{
    protected ?TenantConfig $currentTenant = null;
    protected ?string $currentDomain = null;

    public function resolve(?string $domain = null): ?TenantConfig
    {
        $domain = $domain ?? $this->getCurrentDomain();

        if (!$domain) {
            return null;
        }

        $this->currentDomain = $domain;

        $cacheKey = $this->getCacheKey($domain);
        $cacheTtl = config('payment.tenant_cache_ttl', 3600);

        $this->currentTenant = Cache::remember($cacheKey, $cacheTtl, function () use ($domain) {
            return TenantConfig::findByDomain($domain);
        });

        $this->applyGatewayConfigOverrides();

        if ($this->currentTenant) {
            /*Log::info("Tenant resolved for domain: {$domain}", [
                'tenant_id' => $this->currentTenant->id,
                'payment_gateway' => $this->currentTenant->payment_gateway,
            ]);*/
        } else {
            /*Log::info("No tenant configuration found for domain: {$domain}, using default");*/
        }

        return $this->currentTenant;
    }

    public function getCurrentDomain(): ?string
    {
        if ($this->currentDomain) {
            return $this->currentDomain;
        }

        if (app()->runningInConsole()) {
            return null;
        }

        $host = request()->getHost();

        return $this->normalizeDomain($host);
    }

    public function getTenantConfig(): ?TenantConfig
    {
        return $this->currentTenant;
    }

    public function getPaymentGateway(): ?string
    {
        if (!$this->currentTenant || !$this->currentTenant->is_active) {
            return null;
        }

        return $this->currentTenant->payment_gateway;
    }


    /**
     * Tenants assigned "PayPal (Account 2)" get the srmklive PayPal client
     * pointed at that account's credentials by swapping the global 'paypal'
     * config key for the duration of this request - PayPalGateway itself
     * stays unaware there's more than one account.
     */
    protected function applyGatewayConfigOverrides(): void
    {
        // Matches getPaymentGateway()'s own gating (just "active tenant row"), not
        // shouldUseTenantConfig()'s tenant_mode_enabled check - PaymentGatewayManager
        // gets a separate, never-resolve()'d TenantResolver instance from the
        // container, so in practice it's this class's own state (as populated by
        // the ResolveTenant middleware, which is likewise ungated) that drives
        // whether tenant-specific config actually takes effect for a request.
        if (!$this->currentTenant || !$this->currentTenant->is_active) {
            return;
        }

        if ($this->currentTenant->payment_gateway === Order::PAYMENT_GATEWAY_PAYPAL2) {
            Config::set('paypal', config('paypal2'));
            Config::set('payment.gateways.paypal.webhook_id', config('paypal2.webhook_id'));
        }
    }

    public function hasTenantConfig(): bool
    {
        return $this->currentTenant !== null
            && $this->currentTenant->is_active
            && $this->currentTenant->isGatewayConfigured();
    }

    public function shouldUseTenantConfig(): bool
    {
        return config('payment.tenant_mode_enabled', false) && $this->hasTenantConfig();
    }

    public function clearCache(?string $domain = null): void
    {
        if ($domain) {
            Cache::forget($this->getCacheKey($domain));
        } else {
            Cache::forget($this->getCacheKey($this->currentDomain));
        }
    }

    public static function clearAllTenantCache(): void
    {
        $tenants = TenantConfig::all();

        foreach ($tenants as $tenant) {
            Cache::forget("tenant_config:{$tenant->domain}");
        }
    }

    protected function getCacheKey(string $domain): string
    {
        return "tenant_config:{$domain}";
    }

    protected function normalizeDomain(string $domain): string
    {
        $domain = strtolower(trim($domain));

        $domain = preg_replace('/^www\./', '', $domain);

        if (strpos($domain, ':') !== false) {
            $domain = explode(':', $domain)[0];
        }

        return $domain;
    }

    public function setTenant(TenantConfig $tenant): void
    {
        $this->currentTenant = $tenant;
        $this->currentDomain = $tenant->domain;
    }
}
