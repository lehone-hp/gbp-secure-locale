<?php

namespace Tests\Feature;

use App\Mail\OrderConfirmation;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\Feature\Concerns\StartsCheckout;
use Tests\TestCase;

class MollieCheckoutTest extends TestCase
{
    use RefreshDatabase;
    use StartsCheckout;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'payment.default_gateway' => Order::PAYMENT_GATEWAY_MOLLIE,
            'payment.tenant_mode_enabled' => false,
            'payment.gateways.mollie.api_key' => 'test_key',
        ]);

        Mail::fake();
        $this->withoutVite();
    }

    public function test_process_redirects_to_mollie_hosted_checkout(): void
    {
        $this->fakePaymentCreation();

        $this->postJson($this->signedProcessUrl(), ['payment_gateway' => Order::PAYMENT_GATEWAY_MOLLIE])
            ->assertOk()
            ->assertJson(['redirect_url' => 'https://www.mollie.com/checkout/tr_123']);
    }

    public function test_process_sends_amount_and_order_id_to_mollie(): void
    {
        $this->fakePaymentCreation();

        $this->postJson($this->signedProcessUrl(), ['payment_gateway' => Order::PAYMENT_GATEWAY_MOLLIE]);

        Http::assertSent(fn ($request) => $request['amount']['value'] === '100.00'
            && $request['metadata']['order_id'] === (string) Order::firstOrFail()->id);
    }

    public function test_process_fails_order_when_mollie_rejects_the_request(): void
    {
        Http::fake(['api.mollie.com/v2/payments' => Http::response(['detail' => 'bad'], 422)]);

        $this->postJson($this->signedProcessUrl(), ['payment_gateway' => Order::PAYMENT_GATEWAY_MOLLIE])->assertStatus(422);

        $this->assertSame(Order::PAYMENT_STATUS_FAILED, Order::firstOrFail()->payment_status);
    }

    public function test_webhook_marks_order_paid_and_sends_confirmation_once(): void
    {
        $order = $this->pendingOrder();
        $this->fakePayment($order, 'paid');

        $this->postJson(route('webhooks.mollie'), ['id' => 'tr_123'])->assertOk();
        $this->postJson(route('webhooks.mollie'), ['id' => 'tr_123'])->assertOk();

        Mail::assertQueued(OrderConfirmation::class, 1);
    }

    public function test_webhook_marks_pending_order_failed_when_payment_is_canceled(): void
    {
        $order = $this->pendingOrder();
        $this->fakePayment($order, 'canceled');

        $this->postJson(route('webhooks.mollie'), ['id' => 'tr_123'])->assertOk();

        $this->assertSame(Order::PAYMENT_STATUS_FAILED, $order->fresh()->payment_status);
    }

    public function test_webhook_rejects_malformed_payment_id(): void
    {
        $this->postJson(route('webhooks.mollie'), ['id' => 'forged'])->assertStatus(400);
    }

    public function test_webhook_acknowledges_unknown_payment(): void
    {
        Http::fake(['api.mollie.com/v2/payments/*' => Http::response(['status' => 404], 404)]);

        $this->postJson(route('webhooks.mollie'), ['id' => 'tr_unknown'])->assertOk();
    }

    public function test_success_page_confirms_paid_payment_without_webhook(): void
    {
        $order = $this->pendingOrder();
        $this->fakePayment($order, 'paid');

        $this->get(route('checkout.success', $order))->assertOk();

        $this->assertSame(Order::PAYMENT_STATUS_PAID, $order->fresh()->payment_status);
    }

    public function test_success_page_redirects_to_cancel_when_payment_was_canceled(): void
    {
        $order = $this->pendingOrder();
        $this->fakePayment($order, 'canceled');

        $this->get(route('checkout.success', $order))->assertRedirect(route('checkout.cancel'));
    }

    private function fakePaymentCreation(): void
    {
        Http::fake(['api.mollie.com/v2/payments' => Http::response([
            'id' => 'tr_123',
            '_links' => ['checkout' => ['href' => 'https://www.mollie.com/checkout/tr_123']],
        ])]);
    }

    private function fakePayment(Order $order, string $status): void
    {
        Http::fake(['api.mollie.com/v2/payments/tr_123' => Http::response([
            'id' => 'tr_123',
            'status' => $status,
            'metadata' => ['order_id' => (string) $order->id],
        ])]);
    }

    private function pendingOrder(): Order
    {
        $this->fakePaymentCreation();
        $this->postJson($this->signedProcessUrl(), ['payment_gateway' => Order::PAYMENT_GATEWAY_MOLLIE]);

        return Order::firstOrFail();
    }
}
