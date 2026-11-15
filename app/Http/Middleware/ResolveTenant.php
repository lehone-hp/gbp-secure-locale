<?php

namespace App\Http\Middleware;

use App\Services\TenantResolver;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class ResolveTenant
{
    protected TenantResolver $tenantResolver;

    public function __construct(TenantResolver $tenantResolver)
    {
        $this->tenantResolver = $tenantResolver;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $this->tenantResolver->resolve();

        // Set tenant information in request attributes
        $request->attributes->set('tenant', $this->tenantResolver->getTenantConfig());
        $request->attributes->set('tenant_domain', $this->tenantResolver->getCurrentDomain());
        $request->attributes->set('tenant_domain', $this->tenantResolver->getCurrentDomain());

        // Override payment gateway config for this request
        $gateway = $this->tenantResolver->getPaymentGateway();
        if ($gateway && $gateway !== config('payment.default_gateway')) {
            Config::set('payment.default_gateway', $this->tenantResolver->getPaymentGateway());
        }

        Config::set('app.asset_url', 'https://' . $this->tenantResolver->getCurrentDomain());

        return $next($request);
    }
}
