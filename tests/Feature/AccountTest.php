<?php

namespace Tests\Feature;

use App\Models\AccountRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ServiceAddon;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\Feature\Concerns\StartsCheckout;
use Tests\TestCase;

class AccountTest extends TestCase
{
    use RefreshDatabase;
    use StartsCheckout;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    private function customer(array $attributes = []): User
    {
        return User::factory()->create($attributes + ['role' => User::ROLE_USER, 'email_verified_at' => now()]);
    }

    private function orderFor(string $email, string $itemType = ServiceAddon::PRICING_ONE_TIME, string $paymentStatus = Order::PAYMENT_STATUS_PAID): Order
    {
        $order = Order::create([
            'customer_first_name' => 'Jane', 'customer_last_name' => 'Doe', 'customer_email' => $email,
            'customer_phone' => '1', 'address_line_1' => 'x', 'city' => 'x', 'state' => 'x', 'postal_code' => 'x', 'country' => 'FR',
            'subtotal' => 100, 'total' => 100, 'currency' => 'USD', 'status' => Order::STATUS_PENDING,
            'payment_gateway' => 'stripe', 'payment_status' => $paymentStatus,
        ]);
        OrderItem::create(['order_id' => $order->id, 'service_addon_id' => $this->addon()->id, 'item_name' => 'Basic', 'item_type' => $itemType, 'price' => 100, 'quantity' => 1]);

        return $order;
    }

    public function test_guests_are_sent_to_the_login_page(): void
    {
        $this->get(route('account.orders'))->assertRedirect(route('account.login'));
    }

    public function test_registration_creates_a_customer_with_a_customer_number_and_sends_verification(): void
    {
        Notification::fake();

        $this->post(route('account.register'), ['email' => 'new@example.com', 'password' => 'Str0ngPassword', 'password_confirmation' => 'Str0ngPassword'])
            ->assertRedirect(route('verification.notice'));

        $user = User::where('email', 'new@example.com')->firstOrFail();

        $this->assertSame([User::ROLE_USER, 'CLI-'.str_pad($user->id, 5, '0', STR_PAD_LEFT)], [$user->role, $user->customer_number]);
        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_weak_passwords_are_rejected(): void
    {
        $this->post(route('account.register'), ['email' => 'new@example.com', 'password' => 'short', 'password_confirmation' => 'short'])
            ->assertSessionHasErrors('password');
    }

    public function test_unverified_customers_cannot_reach_the_account_area(): void
    {
        $this->actingAs($this->customer(['email_verified_at' => null]))->get(route('account.orders'))->assertRedirect(route('verification.notice'));
    }

    public function test_following_the_verification_link_confirms_the_email(): void
    {
        $user = $this->customer(['email_verified_at' => null]);
        $url = URL::temporarySignedRoute('verification.verify', now()->addHour(), ['id' => $user->id, 'hash' => sha1($user->email)]);

        $this->actingAs($user)->get($url)->assertRedirect(route('account.orders'));

        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }

    public function test_customers_can_sign_in(): void
    {
        $user = $this->customer(['password' => Hash::make('Str0ngPassword')]);

        $this->post(route('account.login'), ['email' => $user->email, 'password' => 'Str0ngPassword'])->assertRedirect(route('account.orders'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_orders_placed_as_a_guest_with_the_same_email_appear_in_the_account(): void
    {
        $user = $this->customer(['email' => 'jane@example.com']);
        $this->orderFor('jane@example.com');

        $this->actingAs($user)->get(route('account.orders'))->assertSee('Basic');
    }

    public function test_other_customers_orders_are_not_listed(): void
    {
        $this->orderFor('someone-else@example.com');

        $this->actingAs($this->customer())->get(route('account.orders'))->assertDontSee('Basic');
    }

    public function test_other_customers_orders_cannot_be_opened_or_invoiced(): void
    {
        $order = $this->orderFor('someone-else@example.com');
        $customer = $this->customer();

        $this->actingAs($customer)->get(route('account.orders.show', $order))->assertNotFound();
        $this->actingAs($customer)->get(route('account.orders.invoice', $order))->assertNotFound();
    }

    public function test_paid_orders_can_be_downloaded_as_an_invoice(): void
    {
        $user = $this->customer(['email' => 'jane@example.com']);
        $order = $this->orderFor('jane@example.com');

        $this->actingAs($user)->get(route('account.orders.invoice', $order))->assertOk()->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_profile_can_be_updated(): void
    {
        $user = $this->customer();

        $this->actingAs($user)->put(route('account.profile.update'), ['first_name' => 'Ada', 'last_name' => 'Lovelace', 'company' => 'Analytical'])->assertSessionHasNoErrors();

        $this->assertSame(['Ada', 'Lovelace', 'Ada Lovelace'], [$user->fresh()->first_name, $user->fresh()->last_name, $user->fresh()->name]);
    }

    public function test_password_can_be_changed_with_the_current_password(): void
    {
        $user = $this->customer(['password' => Hash::make('Str0ngPassword')]);

        $this->actingAs($user)->put(route('account.security.update'), ['current_password' => 'Str0ngPassword', 'password' => 'An0therPassword', 'password_confirmation' => 'An0therPassword'])->assertSessionHasNoErrors();

        $this->assertTrue(Hash::check('An0therPassword', $user->fresh()->password));
    }

    public function test_password_change_needs_the_correct_current_password(): void
    {
        $user = $this->customer(['password' => Hash::make('Str0ngPassword')]);

        $this->actingAs($user)->put(route('account.security.update'), ['current_password' => 'wrong', 'password' => 'An0therPassword', 'password_confirmation' => 'An0therPassword'])->assertSessionHasErrors('current_password');
    }

    public function test_a_subscription_request_is_stored_and_staff_are_notified(): void
    {
        Mail::fake();
        $user = $this->customer(['email' => 'jane@example.com']);
        $order = $this->orderFor('jane@example.com', ServiceAddon::PRICING_MONTHLY);

        $this->actingAs($user)->post(route('account.requests.store'), ['type' => AccountRequest::TYPE_CANCEL, 'order_id' => $order->id, 'reason' => 'Too busy'])->assertSessionHasNoErrors();

        $this->assertSame([AccountRequest::STATUS_PENDING, $order->id], [AccountRequest::firstOrFail()->status, AccountRequest::firstOrFail()->order_id]);
        Mail::assertSent(\App\Mail\AccountRequestNotification::class);
    }

    public function test_requests_cannot_target_another_customers_subscription(): void
    {
        $order = $this->orderFor('someone-else@example.com', ServiceAddon::PRICING_MONTHLY);

        $this->actingAs($this->customer())->post(route('account.requests.store'), ['type' => AccountRequest::TYPE_CANCEL, 'order_id' => $order->id])->assertSessionHasErrors('order_id');
    }

    public function test_closing_an_account_needs_no_subscription(): void
    {
        Mail::fake();

        $this->actingAs($this->customer())->post(route('account.requests.store'), ['type' => AccountRequest::TYPE_CLOSE])->assertSessionHasNoErrors();

        $this->assertSame(1, AccountRequest::count());
    }

    public function test_customers_cannot_sign_in_to_the_admin(): void
    {
        $user = $this->customer(['password' => Hash::make('Str0ngPassword')]);

        $this->post(route('admin.login'), ['email' => $user->email, 'password' => 'Str0ngPassword']);

        $this->assertGuest('admin');
    }

    public function test_admins_cannot_sign_in_through_the_customer_form(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN, 'password' => Hash::make('Str0ngPassword')]);

        $this->post(route('account.login'), ['email' => $admin->email, 'password' => 'Str0ngPassword'])->assertSessionHasErrors('email');
    }

    public function test_an_admin_can_update_a_request_status(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $accountRequest = AccountRequest::create(['user_id' => $this->customer()->id, 'type' => AccountRequest::TYPE_CLOSE]);

        $this->actingAs($admin, 'admin')->put(route('admin.account-requests.update', $accountRequest), ['status' => AccountRequest::STATUS_DONE]);

        $this->assertSame(AccountRequest::STATUS_DONE, $accountRequest->fresh()->status);
    }
}
