<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_lists_latest_paid_purchases(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Order::create([
            'customer_first_name' => 'Paula', 'customer_last_name' => 'Paid', 'customer_email' => 'p@example.com',
            'customer_phone' => '123', 'address_line_1' => '1 rue de Paris', 'city' => 'Paris', 'country' => 'FR',
            'currency' => 'EUR', 'subtotal' => 100, 'total' => 100,
            'payment_gateway' => 'stripe', 'payment_status' => Order::PAYMENT_STATUS_PAID,
        ]);

        $this->actingAs($admin, 'admin')->get(route('admin.dashboard'))
            ->assertOk()->assertSee('Last 5 Purchases')->assertSee('Paula Paid');
    }
}
