<?php

namespace App\Providers;

use App\Services\PaymentGatewayManager;
use App\Services\TenantResolver;
use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(PaymentGatewayManager::class, function ($app) {
            return new PaymentGatewayManager($app->make(TenantResolver::class));
        });

        $this->app->alias(PaymentGatewayManager::class, 'payment');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/payment.php', 'payment'
        );
    }
}
