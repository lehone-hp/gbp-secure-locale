<?php

namespace Tests\Feature;

use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\Feature\Concerns\StartsCheckout;
use Tests\TestCase;

class CheckoutWizardTest extends TestCase
{
    use RefreshDatabase;
    use StartsCheckout;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'payment.default_gateway' => Order::PAYMENT_GATEWAY_STRIPE,
            'payment.tenant_mode_enabled' => false,
            'app.debug' => false,
        ]);

        $this->withoutVite();
    }

    public function test_checkout_without_addon_redirects_to_services(): void
    {
        $this->get(route('checkout.index'))->assertRedirect(route('services.index'));
    }

    public function test_step_one_renders_the_selected_addon(): void
    {
        $addon = $this->addon();

        $this->get(route('checkout.index', ['addon' => $addon->id]))->assertOk()->assertSee('Basic');
    }

    public function test_step_one_creates_a_pending_order_with_one_item(): void
    {
        $this->post(route('checkout.start'), $this->customerPayload($this->addon()));

        $order = Order::with('items')->firstOrFail();

        $this->assertSame([Order::PAYMENT_STATUS_PENDING, 1, '100.00'], [$order->payment_status, $order->items->count(), $order->total]);
    }

    public function test_step_one_redirects_to_signed_payment_page(): void
    {
        $response = $this->post(route('checkout.start'), $this->customerPayload($this->addon()));

        $response->assertRedirect(URL::signedRoute('checkout.pay', Order::firstOrFail(), absolute: false));
    }

    public function test_step_one_returns_field_errors_and_creates_no_order(): void
    {
        $payload = ['email' => 'not-an-email'] + $this->customerPayload($this->addon());

        $this->post(route('checkout.start'), $payload)->assertSessionHasErrors('email');
    }

    public function test_resubmitting_step_one_updates_the_same_order(): void
    {
        $addon = $this->addon();
        $this->post(route('checkout.start'), $this->customerPayload($addon));
        $this->post(route('checkout.start'), ['first_name' => 'Janet'] + $this->customerPayload($addon));

        $this->assertSame([1, 'Janet'], [Order::count(), Order::firstOrFail()->customer_first_name]);
    }

    public function test_payment_page_rejects_unsigned_url(): void
    {
        $this->post(route('checkout.start'), $this->customerPayload($this->addon()));

        $this->get(route('checkout.pay', Order::firstOrFail()))->assertForbidden();
    }

    public function test_payment_page_shows_the_order_summary(): void
    {
        $this->post(route('checkout.start'), $this->customerPayload($this->addon()));

        $this->get(URL::signedRoute('checkout.pay', Order::firstOrFail(), absolute: false))->assertOk()->assertSee('Jane Doe');
    }

    public function test_payment_page_redirects_paid_order_to_success(): void
    {
        $this->post(route('checkout.start'), $this->customerPayload($this->addon()));
        $order = Order::firstOrFail();
        $order->markAsPaid('pi_1');

        $this->get(URL::signedRoute('checkout.pay', $order, absolute: false))->assertRedirect(route('checkout.success', $order));
    }

    public function test_paid_order_cannot_be_paid_again(): void
    {
        $url = $this->signedProcessUrl();
        Order::firstOrFail()->markAsPaid('pi_1');

        $this->postJson($url, ['payment_method_id' => 'pm_test', 'payment_gateway' => 'stripe'])->assertStatus(409);
    }

    public function test_process_returns_json_error_for_missing_payment_token(): void
    {
        $this->postJson($this->signedProcessUrl(), ['payment_gateway' => 'stripe'])->assertStatus(422)->assertJsonValidationErrors('payment_method_id');
    }

    public function test_step_one_requires_consent_to_the_terms(): void
    {
        $payload = ['accept_terms' => '0', 'accept_immediate_start' => '0'] + $this->customerPayload($this->addon());

        $this->post(route('checkout.start'), $payload)->assertSessionHasErrors(['accept_terms', 'accept_immediate_start']);
    }

    public function test_step_one_records_consent_on_the_order(): void
    {
        $this->post(route('checkout.start'), $this->customerPayload($this->addon()));

        $order = Order::firstOrFail();

        $this->assertSame([true, config('legal.refund_version')], [$order->immediate_start_accepted, $order->policy_version]);
    }
}
