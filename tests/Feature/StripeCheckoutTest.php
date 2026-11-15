<?php

namespace Tests\Feature;

use App\Mail\OrderConfirmation;
use App\Models\Order;
use App\Services\PaymentGateways\StripeGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Mockery\MockInterface;
use Tests\Feature\Concerns\StartsCheckout;
use Tests\TestCase;

class StripeCheckoutTest extends TestCase
{
    use RefreshDatabase;
    use StartsCheckout;

    protected MockInterface $gateway;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'payment.default_gateway' => Order::PAYMENT_GATEWAY_STRIPE,
            'payment.tenant_mode_enabled' => false,
        ]);

        Mail::fake();
        $this->withoutVite();

        $this->gateway = $this->mock(StripeGateway::class);
    }

    public function test_process_returns_client_secret_when_3d_secure_is_required(): void
    {
        $this->gateway->shouldReceive('createPayment')->once()->andReturn([
            'success' => false,
            'requires_action' => true,
            'transaction_id' => 'pi_3ds',
            'client_secret' => 'pi_3ds_secret_abc',
            'error' => '3D Secure authentication required',
        ]);

        $response = $this->postJson($this->signedProcessUrl(), ['payment_gateway' => Order::PAYMENT_GATEWAY_STRIPE, 'payment_method_id' => 'pm_test']);

        $response->assertOk()->assertJson([
            'requires_action' => true,
            'client_secret' => 'pi_3ds_secret_abc',
            'redirect_url' => route('checkout.success', Order::firstOrFail()->id),
        ]);
    }

    public function test_process_keeps_order_pending_while_3d_secure_is_in_progress(): void
    {
        $this->gateway->shouldReceive('createPayment')->once()->andReturn([
            'success' => false,
            'requires_action' => true,
            'transaction_id' => 'pi_3ds',
            'client_secret' => 'pi_3ds_secret_abc',
            'error' => '3D Secure authentication required',
        ]);

        $this->postJson($this->signedProcessUrl(), ['payment_gateway' => Order::PAYMENT_GATEWAY_STRIPE, 'payment_method_id' => 'pm_test']);

        $this->assertEquals(
            [Order::PAYMENT_STATUS_PENDING, 'pi_3ds'],
            [Order::firstOrFail()->payment_status, Order::firstOrFail()->payment_transaction_id]
        );
    }

    public function test_process_returns_json_error_and_fails_order_when_card_is_declined(): void
    {
        $this->gateway->shouldReceive('createPayment')->once()->andReturn([
            'success' => false,
            'error' => 'Your card was declined.',
        ]);

        $response = $this->postJson($this->signedProcessUrl(), ['payment_gateway' => Order::PAYMENT_GATEWAY_STRIPE, 'payment_method_id' => 'pm_test']);

        $response->assertStatus(422)->assertJson(['success' => false, 'error' => 'Your card was declined.']);
        $this->assertSame(Order::PAYMENT_STATUS_FAILED, Order::firstOrFail()->payment_status);
    }

    public function test_process_pays_order_and_sends_confirmation_when_no_3d_secure_is_needed(): void
    {
        $this->gateway->shouldReceive('createPayment')->once()->andReturn([
            'success' => true,
            'transaction_id' => 'pi_ok',
            'response' => ['status' => 'succeeded'],
        ]);

        $response = $this->postJson($this->signedProcessUrl(), ['payment_gateway' => Order::PAYMENT_GATEWAY_STRIPE, 'payment_method_id' => 'pm_test']);

        $response->assertOk()->assertJson(['success' => true]);
        Mail::assertQueued(OrderConfirmation::class, 1);
        $this->assertSame(Order::PAYMENT_STATUS_PAID, Order::firstOrFail()->payment_status);
    }

    public function test_success_page_marks_order_paid_and_sends_confirmation_once_stripe_confirms(): void
    {
        $order = $this->pendingStripeOrder();
        $this->gateway->shouldReceive('retrievePayment')->with('pi_3ds')->once()->andReturn([
            'success' => true,
            'response' => ['status' => 'succeeded'],
        ]);

        $this->get(route('checkout.success', $order))->assertOk();

        Mail::assertQueued(OrderConfirmation::class, 1);
        $this->assertSame(Order::PAYMENT_STATUS_PAID, $order->fresh()->payment_status);
    }

    public function test_success_page_leaves_order_pending_when_stripe_has_not_confirmed(): void
    {
        $order = $this->pendingStripeOrder();
        $this->gateway->shouldReceive('retrievePayment')->once()->andReturn([
            'success' => false,
            'status' => 'requires_action',
        ]);

        $this->get(route('checkout.success', $order))->assertRedirect(route('checkout.cancel'));

        $this->assertSame(Order::PAYMENT_STATUS_PENDING, $order->fresh()->payment_status);
    }

    public function test_webhook_marks_pending_order_paid_and_sends_confirmation(): void
    {
        $order = $this->pendingStripeOrder();
        $this->gateway->shouldReceive('verifyWebhook')->andReturnTrue();

        $this->postJson(route('webhooks.stripe'), $this->succeededEvent($order))->assertOk();

        Mail::assertQueued(OrderConfirmation::class, 1);
        $this->assertSame(Order::PAYMENT_STATUS_PAID, $order->fresh()->payment_status);
    }

    public function test_webhook_does_not_resend_confirmation_for_an_already_paid_order(): void
    {
        $order = $this->pendingStripeOrder();
        $order->markAsPaid('pi_3ds');
        $this->gateway->shouldReceive('verifyWebhook')->andReturnTrue();

        $this->postJson(route('webhooks.stripe'), $this->succeededEvent($order))->assertOk();

        Mail::assertNothingQueued();
    }

    public function test_mark_as_paid_if_pending_only_succeeds_once(): void
    {
        $order = $this->pendingStripeOrder();

        $this->assertSame(
            [true, false],
            [$order->markAsPaidIfPending('pi_3ds'), Order::findOrFail($order->id)->markAsPaidIfPending('pi_3ds')]
        );
    }

    protected function pendingStripeOrder(): Order
    {
        return Order::create([
            'customer_first_name' => 'Jane',
            'customer_last_name' => 'Doe',
            'customer_email' => 'jane@example.com',
            'customer_phone' => '+33123456789',
            'address_line_1' => '1 rue de Paris',
            'city' => 'Paris',
            'state' => 'IDF',
            'postal_code' => '75001',
            'country' => 'FR',
            'subtotal' => 100,
            'total' => 100,
            'currency' => Order::CURRENCY_USD,
            'status' => Order::STATUS_PENDING,
            'payment_gateway' => Order::PAYMENT_GATEWAY_STRIPE,
            'payment_status' => Order::PAYMENT_STATUS_PENDING,
            'payment_transaction_id' => 'pi_3ds',
        ]);
    }

    protected function succeededEvent(Order $order): array
    {
        return [
            'type' => 'payment_intent.succeeded',
            'data' => ['object' => [
                'id' => 'pi_3ds',
                'status' => 'succeeded',
                'metadata' => ['order_id' => $order->id],
            ]],
        ];
    }
}
