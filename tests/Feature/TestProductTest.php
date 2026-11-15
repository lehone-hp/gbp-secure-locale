<?php

namespace Tests\Feature;

use App\Models\Service;
use Database\Seeders\ServiceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TestProductTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->seed(ServiceSeeder::class);
    }

    public function test_hidden_test_service_is_not_listed_publicly(): void
    {
        $this->get(route('services.index'))->assertDontSee('TEST PAYMENT');
    }

    public function test_hidden_test_service_is_reachable_by_direct_link(): void
    {
        $this->get(route('services.show', 'test-payment'))->assertOk()->assertSee('TEST PAYMENT');
    }

    public function test_hidden_test_service_is_excluded_from_visible_scope(): void
    {
        $this->assertFalse(Service::visible()->where('slug', 'test-payment')->exists());
    }

    public function test_test_addon_costs_one_dollar(): void
    {
        $addon = Service::where('slug', 'test-payment')->firstOrFail()->addons()->firstOrFail();

        $this->assertEquals(1, $addon->price);
    }
}
