<?php

namespace Tests\Feature;

use App\Models\ContractTemplate;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Services\ContractService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Concerns\StartsCheckout;
use Tests\TestCase;

class ContractTest extends TestCase
{
    use RefreshDatabase;
    use StartsCheckout;

    private function order(string $status = Order::PAYMENT_STATUS_PAID, string $name = 'Jane'): Order
    {
        $order = Order::create([
            'customer_first_name' => $name, 'customer_last_name' => 'Doe',
            'customer_email' => 'jane@example.com', 'customer_phone' => '123',
            'address_line_1' => '1 rue de Paris', 'city' => 'Paris', 'country' => 'FR',
            'currency' => 'EUR', 'subtotal' => 100, 'total' => 100,
            'payment_gateway' => 'stripe', 'payment_status' => $status,
        ]);
        OrderItem::create(['order_id' => $order->id, 'service_addon_id' => $this->addon()->id, 'item_name' => 'Audit', 'price' => 100, 'quantity' => 1, 'subtotal' => 100]);

        return $order;
    }

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    public function test_paid_order_downloads_contract_pdf(): void
    {
        $response = $this->actingAs($this->admin(), 'admin')->get(route('admin.orders.contract', $this->order()));

        $response->assertOk()->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_unpaid_order_has_no_contract(): void
    {
        $this->actingAs($this->admin(), 'admin')
            ->get(route('admin.orders.contract', $this->order(Order::PAYMENT_STATUS_PENDING)))
            ->assertNotFound();
    }

    public function test_guest_cannot_download_contract(): void
    {
        $this->get(route('admin.orders.contract', $this->order()))->assertRedirect(route('admin.login'));
    }

    public function test_placeholders_are_replaced_and_escaped(): void
    {
        $html = app(ContractService::class)->render($this->order(name: '<b>Eve</b>'), '{{client_name}} {{service_name}} {{unknown}}');

        $this->assertSame('&lt;b&gt;Eve&lt;/b&gt; Doe Basic {{unknown}}', $html);
    }

    public function test_service_name_prefers_french_with_english_fallback(): void
    {
        $order = $this->order();
        $addon = $order->items->first()->serviceAddon;
        $service = app(ContractService::class);

        $this->assertSame('Basic', $service->render($order, '{{service_name}}'));

        $addon->setTranslation('name', 'fr', 'Basique')->save();

        $this->assertSame('Basique', $service->render($order->fresh(), '{{service_name}}'));
    }

    public function test_saving_creates_new_active_version(): void
    {
        $this->actingAs($this->admin(), 'admin')
            ->put(route('admin.contract-template.update'), ['body' => '<p>New</p>'])
            ->assertRedirect();

        $this->assertSame('v2.1', ContractTemplate::active()->version);
    }

    public function test_preview_works_for_pending_order(): void
    {
        $order = $this->order(Order::PAYMENT_STATUS_PENDING);

        $this->actingAs($this->admin(), 'admin')
            ->put(route('admin.contract-template.preview'), ['body' => '<p>{{client_name}}</p>', 'order_id' => $order->id])
            ->assertOk()->assertHeader('Content-Type', 'application/pdf');
    }
}
