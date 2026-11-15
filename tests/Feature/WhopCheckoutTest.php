<?php

namespace Tests\Feature;

use App\Mail\OrderConfirmation;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\Feature\Concerns\StartsCheckout;
use Tests\TestCase;

class WhopCheckoutTest extends TestCase
{
    use RefreshDatabase;
    use StartsCheckout;

    private const SECRET = 'ws_test_secret';

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'payment.default_gateway' => Order::PAYMENT_GATEWAY_WHOP,
            'payment.tenant_mode_enabled' => false,
            'payment.gateways.whop.api_key' => 'apik_test',
            'payment.gateways.whop.company_id' => 'biz_test',
            'payment.gateways.whop.webhook_secret' => self::SECRET,
        ]);

        Mail::fake();
        $this->withoutVite();
    }

    public function test_process_returns_checkout_configuration_id_for_embedded_checkout(): void
    {
        Http::fake(['*/checkout_configurations' => Http::response(['id' => 'ch_123'])]);

        $response = $this->postJson($this->signedProcessUrl(), ['payment_gateway' => Order::PAYMENT_GATEWAY_WHOP]);

        $response->assertOk()->assertJson([
            'embedded_checkout_id' => 'ch_123',
            'redirect_url' => route('checkout.success', Order::firstOrFail()->id),
        ]);
    }

    public function test_process_sends_order_id_metadata_to_whop(): void
    {
        Http::fake(['*/checkout_configurations' => Http::response(['id' => 'ch_123'])]);

        $this->postJson($this->signedProcessUrl(), ['payment_gateway' => Order::PAYMENT_GATEWAY_WHOP]);

        Http::assertSent(fn ($request) => $request['metadata']['order_id'] === (string) Order::firstOrFail()->id);
    }

    public function test_process_fails_order_when_whop_rejects_the_request(): void
    {
        Http::fake(['*/checkout_configurations' => Http::response(['error' => 'bad'], 422)]);

        $this->postJson($this->signedProcessUrl(), ['payment_gateway' => Order::PAYMENT_GATEWAY_WHOP])->assertStatus(422);

        $this->assertSame(Order::PAYMENT_STATUS_FAILED, Order::firstOrFail()->payment_status);
    }

    public function test_webhook_marks_order_paid_and_sends_confirmation_once(): void
    {
        $order = $this->pendingOrder();
        $body = $this->event($order);

        $this->signedPost($body, 'msg_1')->assertOk();
        $this->signedPost($body, 'msg_1')->assertOk();

        Mail::assertQueued(OrderConfirmation::class, 1);
        $this->assertSame(Order::PAYMENT_STATUS_PAID, $order->fresh()->payment_status);
    }

    public function test_webhook_rejects_invalid_signature(): void
    {
        $order = $this->pendingOrder();

        $this->postJson(route('webhooks.whop'), $this->event($order), [
            'webhook-id' => 'msg_2',
            'webhook-timestamp' => (string) time(),
            'webhook-signature' => 'v1,forged',
        ])->assertForbidden();
    }

    public function test_webhook_rejects_stale_timestamp(): void
    {
        $order = $this->pendingOrder();

        $this->signedPost($this->event($order), 'msg_3', time() - 3600)->assertForbidden();
    }

    private function signedPost(array $body, string $id, ?int $timestamp = null)
    {
        $timestamp ??= time();
        $raw = json_encode($body);
        $signature = 'v1,' . base64_encode(hash_hmac('sha256', "{$id}.{$timestamp}.{$raw}", self::SECRET, true));

        return $this->call('POST', route('webhooks.whop'), [], [], [], [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_WEBHOOK_ID' => $id,
            'HTTP_WEBHOOK_TIMESTAMP' => (string) $timestamp,
            'HTTP_WEBHOOK_SIGNATURE' => $signature,
        ], $raw);
    }

    private function event(Order $order): array
    {
        return [
            'id' => 'msg_x',
            'type' => 'payment.succeeded',
            'data' => ['id' => 'pay_123', 'metadata' => ['order_id' => (string) $order->id]],
        ];
    }

    private function pendingOrder(): Order
    {
        Http::fake(['*/checkout_configurations' => Http::response(['id' => 'ch_123'])]);
        $this->postJson($this->signedProcessUrl(), ['payment_gateway' => Order::PAYMENT_GATEWAY_WHOP]);

        return Order::firstOrFail();
    }
}
