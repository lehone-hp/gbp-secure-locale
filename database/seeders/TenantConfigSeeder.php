<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\TenantConfig;
use Illuminate\Database\Seeder;

class TenantConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenants = [
            [
                'domain' => 'gbp-securelocal.com',
                'payment_gateway' => Order::PAYMENT_GATEWAY_STRIPE,
                'is_active' => true,
            ],
            [
                'domain' => 'gbp-securelocal.net',
                'payment_gateway' => Order::PAYMENT_GATEWAY_SQUARE,
                'is_active' => true,
            ],
            [
                'domain' => 'gbp-securelocal.org',
                'payment_gateway' => Order::PAYMENT_GATEWAY_PAYPAL,
                'is_active' => true,
            ],
        ];

        foreach ($tenants as $tenant) {
            TenantConfig::updateOrCreate(
                ['domain' => $tenant['domain']],
                $tenant
            );
        }

        $this->command->info('Tenant configurations seeded successfully!');
        $this->command->info('Each domain is now configured to use a specific payment gateway.');
        $this->command->warn('Make sure all gateway credentials are properly set in your .env file.');
    }
}
