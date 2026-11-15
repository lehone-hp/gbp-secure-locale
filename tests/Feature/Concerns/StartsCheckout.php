<?php

namespace Tests\Feature\Concerns;

use App\Models\Order;
use App\Models\Service;
use App\Models\ServiceAddon;
use Illuminate\Support\Facades\URL;

trait StartsCheckout
{
    protected function addon(): ServiceAddon
    {
        $service = Service::create(['name' => 'Audit', 'slug' => 'audit', 'caption' => 'Caption', 'description' => 'Description']);

        return ServiceAddon::create([
            'name' => 'Basic',
            'description' => 'Description',
            'price' => 100,
            'service_id' => $service->id,
            'type' => ServiceAddon::PRICING_ONE_TIME,
        ]);
    }

    protected function customerPayload(ServiceAddon $addon): array
    {
        return [
            'addon_id' => $addon->id,
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'jane@example.com',
            'phone' => '+33123456789',
            'address_line_1' => '1 rue de Paris',
            'city' => 'Paris',
            'state' => 'IDF',
            'postal_code' => '75001',
            'country' => 'FR',
            'accept_terms' => '1',
            'accept_immediate_start' => '1',
        ];
    }

    /**
     * Run step 1 and return the signed step 2 payment URL for the new pending order.
     */
    protected function signedProcessUrl(): string
    {
        $this->post(route('checkout.start'), $this->customerPayload($this->addon()))->assertRedirect();

        return URL::signedRoute('checkout.process', Order::firstOrFail(), absolute: false);
    }
}
