<?php

namespace App\Http\Controllers\Web\Account;

use App\Http\Controllers\Controller;
use App\Mail\AccountRequestNotification;
use App\Models\AccountRequest;
use App\Models\Order;
use App\Models\ServiceAddon;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password as PasswordRule;

class AccountController extends Controller
{
    private const RECURRING_TYPES = [
        ServiceAddon::PRICING_MONTHLY,
        ServiceAddon::PRICING_THREE_MONTHS,
        ServiceAddon::PRICING_SIX_MONTHS,
        ServiceAddon::PRICING_ANNUAL,
    ];

    public function orders(Request $request)
    {
        $orders = $request->user()->orders()->with('items')->latest()->get();

        return view('web.account.orders', compact('orders'));
    }

    public function showOrder(Request $request, Order $order)
    {
        $this->authorizeOrder($request, $order);

        return view('web.account.order', ['order' => $order->load('items')]);
    }

    public function invoice(Request $request, Order $order, InvoiceService $invoices)
    {
        $this->authorizeOrder($request, $order);

        abort_unless($order->payment_status === Order::PAYMENT_STATUS_PAID, 404);

        return response($invoices->generate($order), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$invoices->filename($order).'"',
        ]);
    }

    public function profile(Request $request)
    {
        return view('web.account.profile', ['user' => $request->user(), 'countries' => config('constants.countries')]);
    }

    public function updateProfile(Request $request)
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'company' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:100'],
        ]);

        $request->user()->update($data + ['name' => $data['first_name'].' '.$data['last_name']]);

        return back()->with('success', __('account.profile_saved'));
    }

    public function security()
    {
        return view('web.account.security');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', PasswordRule::min(10)->mixedCase()->numbers()],
        ]);

        $request->user()->update(['password' => $request->password]);

        return back()->with('success', __('account.password_saved'));
    }

    public function requests(Request $request)
    {
        return view('web.account.requests', [
            'subscriptions' => $this->subscriptionOrders($request),
            'requests' => $request->user()->accountRequests()->with('order')->latest()->get(),
        ]);
    }

    public function storeRequest(Request $request)
    {
        $data = $request->validate([
            'type' => ['required', Rule::in(AccountRequest::TYPES)],
            'order_id' => [
                Rule::requiredIf($request->type !== AccountRequest::TYPE_CLOSE),
                'nullable',
                Rule::in($this->subscriptionOrders($request)->pluck('id')->all()),
            ],
            'reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $accountRequest = $request->user()->accountRequests()->create([
            'type' => $data['type'],
            'order_id' => $data['type'] === AccountRequest::TYPE_CLOSE ? null : $data['order_id'],
            'reason' => $data['reason'] ?? null,
        ]);

        try {
            Mail::to(settings()->get('notify_email'))->send(new AccountRequestNotification($accountRequest));
        } catch (\Throwable $e) {
            Log::error('Account request email failed: '.$e->getMessage());
        }

        return back()->with('success', __('account.request_sent'));
    }

    /**
     * Paid orders that contain a recurring item, the only ones a subscription request can target.
     */
    private function subscriptionOrders(Request $request)
    {
        return $request->user()->orders()
            ->where('payment_status', Order::PAYMENT_STATUS_PAID)
            ->whereHas('items', fn ($items) => $items->whereIn('item_type', self::RECURRING_TYPES))
            ->with('items')
            ->latest()
            ->get();
    }

    private function authorizeOrder(Request $request, Order $order): void
    {
        abort_unless(strcasecmp($order->customer_email, $request->user()->email) === 0, 404);
    }
}
