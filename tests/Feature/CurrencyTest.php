<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Services\CurrencyManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\StartsCheckout;
use Tests\TestCase;

class CurrencyTest extends TestCase
{
    use RefreshDatabase;
    use StartsCheckout;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'payment.default_gateway' => Order::PAYMENT_GATEWAY_STRIPE,
            'payment.tenant_mode_enabled' => false,
            'currency.rates.CAD' => 1.5,
            'app.debug' => false,
        ]);

        $this->withoutVite();
    }

    public function test_switching_currency_stores_it_in_the_session(): void
    {
        $this->from('/')->get(route('currency.switch', 'cad'))->assertRedirect('/');

        $this->assertSame('CAD', app(CurrencyManager::class)->current());
    }

    public function test_unsupported_currency_is_rejected(): void
    {
        $this->get(route('currency.switch', 'JPY'))->assertStatus(400);
    }

    public function test_conversion_uses_the_configured_rate_and_rounds_to_cents(): void
    {
        $this->assertSame(150.0, app(CurrencyManager::class)->convert(100, 'CAD'));
    }

    public function test_order_is_priced_and_charged_in_the_selected_currency(): void
    {
        $this->withSession(['currency' => 'CAD'])->post(route('checkout.start'), $this->customerPayload($this->addon()));

        $order = Order::firstOrFail();

        $this->assertSame(['CAD', '150.00', 1.5], [$order->currency, $order->total, (float) $order->exchange_rate]);
    }

    public function test_order_falls_back_to_usd_when_the_gateway_does_not_support_the_currency(): void
    {
        config(['payment.default_gateway' => Order::PAYMENT_GATEWAY_PAYUNIT]);

        $this->withSession(['currency' => 'CAD'])->post(route('checkout.start'), $this->customerPayload($this->addon()));

        $this->assertSame(['USD', '100.00'], [Order::firstOrFail()->currency, Order::firstOrFail()->total]);
    }

    public function test_changing_currency_reprices_the_pending_order(): void
    {
        $addon = $this->addon();

        $this->withSession(['currency' => 'CAD'])->post(route('checkout.start'), $this->customerPayload($addon));
        $this->withSession(['currency' => 'USD'])->post(route('checkout.start'), $this->customerPayload($addon));

        $this->assertSame(['USD', '100.00'], [Order::firstOrFail()->currency, Order::firstOrFail()->total]);
    }

    public function test_admin_rate_setting_overrides_the_config_default(): void
    {
        settings()->set(['rate_eur' => '0.5']);

        $this->assertSame(50.0, app(CurrencyManager::class)->convert(100, 'EUR'));
    }
}
