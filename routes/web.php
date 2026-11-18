<?php

use App\Http\Controllers\Admin\Auth\LoginController as AdminLoginController;
use App\Http\Controllers\Admin\Dashboard\DashboardController;
use App\Http\Controllers\Admin\Faq\FaqController;
use App\Http\Controllers\Admin\ContractTemplateController;
use App\Http\Controllers\Admin\Order\OrderController;
use App\Http\Controllers\Admin\Page\PageController;
use App\Http\Controllers\Admin\Profile\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\Achievement\CertificationController;
use App\Http\Controllers\Admin\Achievement\ClientController;
use App\Http\Controllers\Admin\Service\ServiceController;
use App\Http\Controllers\Admin\Service\ServiceAddonController;
use App\Http\Controllers\Admin\Settings\SettingController;
use App\Http\Controllers\Admin\Staff\StaffController;
use App\Http\Controllers\Admin\Account\AccountRequestController;
use App\Http\Controllers\Web\Account\AccountController;
use App\Http\Controllers\Web\Account\AuthController as AccountAuthController;
use App\Http\Controllers\Web\AchievementController;
use App\Http\Controllers\Web\CheckoutController;
use App\Http\Controllers\Web\ContactController;
use App\Http\Controllers\Web\CurrencyController;
use App\Http\Controllers\Web\LocaleController;
use App\Http\Controllers\Web\WebhookController;
use App\Http\Controllers\Web\WebPageController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/', [WebPageController::class, 'index'])->name('index');
Route::get('/terms-and-conditions', [WebPageController::class, 'termsAndConditions'])->name('terms-and-conditions');
Route::get('/privacy-policy', [WebPageController::class, 'privacyPolicy'])->name('privacy-policy');
Route::get('/refund-policy', [WebPageController::class, 'refundPolicy'])->name('refund-policy');
Route::get('/legal-notice', [WebPageController::class, 'legalNotice'])->name('legal-notice');

// Language switching
Route::get('/locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');

// Currency switching
Route::get('/currency/{code}', [CurrencyController::class, 'switch'])->name('currency.switch');

// Checkout routes
Route::get('/services/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/services/checkout', [CheckoutController::class, 'start'])->name('checkout.start');
// Relative signatures: a proxy or tenant domain changing host/scheme can't invalidate them
Route::middleware('signed:relative')->group(function () {
    Route::get('/services/checkout/{order}/pay', [CheckoutController::class, 'pay'])->name('checkout.pay');
    Route::post('/services/checkout/{order}/process', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::post('/services/checkout/{order}/paypal/create-order', [CheckoutController::class, 'createPayPalOrder'])->name('checkout.paypal.create');
});
Route::post('/services/checkout/paypal/capture-order', [CheckoutController::class, 'capturePayPalOrder'])->name('checkout.paypal.capture');
Route::get('/services/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');
Route::get('/services/checkout/cancel', [CheckoutController::class, 'cancel'])->name('checkout.cancel');

// Payment webhooks
Route::post('/webhooks/braintree', [WebhookController::class, 'braintree'])->name('webhooks.braintree');
Route::post('/webhooks/stripe', [WebhookController::class, 'stripe'])->name('webhooks.stripe');
Route::post('/webhooks/paypal', [WebhookController::class, 'paypal'])->name('webhooks.paypal');
Route::post('/webhooks/paypal2', [WebhookController::class, 'paypal2'])->name('webhooks.paypal2');
Route::post('/webhooks/payunit', [WebhookController::class, 'payunit'])->name('webhooks.payunit');
Route::post('/webhooks/square', [WebhookController::class, 'square'])->name('webhooks.square');
Route::post('/webhooks/whop', [WebhookController::class, 'whop'])->name('webhooks.whop');
Route::post('/webhooks/mollie', [WebhookController::class, 'mollie'])->name('webhooks.mollie');

// Customer account
Route::prefix('account')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AccountAuthController::class, 'showLogin'])->name('account.login');
        Route::post('/login', [AccountAuthController::class, 'login'])->middleware('throttle:6,1');
        Route::get('/register', [AccountAuthController::class, 'showRegister'])->name('account.register');
        Route::post('/register', [AccountAuthController::class, 'register'])->middleware('throttle:6,1');
        Route::get('/forgot-password', [AccountAuthController::class, 'showForgotPassword'])->name('password.request');
        Route::post('/forgot-password', [AccountAuthController::class, 'sendResetLink'])->middleware('throttle:6,1')->name('password.email');
        Route::get('/reset-password/{token}', [AccountAuthController::class, 'showResetPassword'])->name('password.reset');
        Route::post('/reset-password', [AccountAuthController::class, 'resetPassword'])->name('password.update');
    });

    Route::middleware('auth')->group(function () {
        Route::post('/logout', [AccountAuthController::class, 'logout'])->name('account.logout');
        Route::get('/email/verify', [AccountAuthController::class, 'verificationNotice'])->name('verification.notice');
        Route::get('/email/verify/{id}/{hash}', [AccountAuthController::class, 'verify'])->middleware('signed')->name('verification.verify');
        Route::post('/email/verification-notification', [AccountAuthController::class, 'resendVerification'])->middleware('throttle:6,1')->name('verification.send');
    });

    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/', fn () => redirect()->route('account.orders'))->name('account.index');
        Route::get('/orders', [AccountController::class, 'orders'])->name('account.orders');
        Route::get('/orders/{order}', [AccountController::class, 'showOrder'])->name('account.orders.show');
        Route::get('/orders/{order}/invoice', [AccountController::class, 'invoice'])->name('account.orders.invoice');
        Route::get('/profile', [AccountController::class, 'profile'])->name('account.profile');
        Route::put('/profile', [AccountController::class, 'updateProfile'])->name('account.profile.update');
        Route::get('/security', [AccountController::class, 'security'])->name('account.security');
        Route::put('/security', [AccountController::class, 'updatePassword'])->name('account.security.update');
        Route::get('/requests', [AccountController::class, 'requests'])->name('account.requests');
        Route::post('/requests', [AccountController::class, 'storeRequest'])->name('account.requests.store');
    });
});

Route::get('/services', [WebPageController::class, 'services'])->name('services.index');
Route::get('/services/{slug}', [WebPageController::class, 'serviceDetail'])->name('services.show');

// Achievements page
Route::get('/achievements', [AchievementController::class, 'index'])->name('achievements.index');

Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');

// Email Preview Routes (Development only)
Route::get('/preview/email/order-confirmation/{order?}', function (\Illuminate\Http\Request $request, $orderId = null) {
    $email = $request->query('email');

    if (!$email && !config('app.debug')) {
        abort(404);
    }

    $order = $orderId
        ? \App\Models\Order::with('items.serviceAddon')->findOrFail($orderId)
        : \App\Models\Order::with('items.serviceAddon')->latest()->first();

    if (!$order) {
        return 'No orders found. Please create an order first.';
    }

    if ($email) {
        \Illuminate\Support\Facades\Mail::to($email)->send(new \App\Mail\OrderConfirmation($order));
        return "Order confirmation email sent to {$email}.";
    }

    return new \App\Mail\OrderConfirmation($order);
})->name('preview.email.order-confirmation');

Route::get('/preview/pdf/order-confirmation/{order?}', function (\Illuminate\Http\Request $request, $orderId = null) {
    if (!config('app.debug')) {
        abort(404);
    }

    $order = $orderId
        ? \App\Models\Order::with('items')->findOrFail($orderId)
        : \App\Models\Order::with('items')->latest()->first();

    if (!$order) {
        return 'No orders found. Please create an order first.';
    }

    $service  = app(\App\Services\InvoiceService::class);
    $pdf      = $service->generate($order);
    $filename = $service->filename($order);

    $disposition = $request->boolean('download') ? 'attachment' : 'inline';

    return response($pdf, 200, [
        'Content-Type'        => 'application/pdf',
        'Content-Disposition' => "{$disposition}; filename=\"{$filename}\"",
    ]);
})->name('preview.pdf.order-confirmation');

/*======================
  ADMIN ROUTES
========================*/
Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {

    Route::group(['namespace' => 'Auth'], function () {
        Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AdminLoginController::class, 'login'])->name('login');
    });

    Route::group(['middleware' => ['admin']], function () {

        //Dashboard
        Route::get('/', function () {
            return redirect()->route('admin.dashboard');
        })->name('index');

        Route::get('/dashboard', [DashboardController::class, 'getDashboard'])->name('dashboard');

        // Services
        Route::resource('services', ServiceController::class);
        Route::get('services/{service}/addons', [ServiceAddonController::class, 'index'])->name('services.addons.index');
        Route::get('services/{service}/addons/create', [ServiceAddonController::class, 'create'])->name('services.addons.create');
        Route::post('services/{service}/addons', [ServiceAddonController::class, 'store'])->name('services.addons.store');
        Route::get('addons/{addon}/edit', [ServiceAddonController::class, 'edit'])->name('addons.edit');
        Route::put('addons/{addon}', [ServiceAddonController::class, 'update'])->name('addons.update');
        Route::delete('addons/{addon}', [ServiceAddonController::class, 'destroy'])->name('addons.destroy');

        // Orders
        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::get('orders/{order}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');
        Route::get('orders/{order}/contract', [OrderController::class, 'contract'])->name('orders.contract');
        Route::get('contract-template', [ContractTemplateController::class, 'edit'])->name('contract-template.edit');
        Route::put('contract-template', [ContractTemplateController::class, 'update'])->name('contract-template.update');
        Route::put('contract-template/preview', [ContractTemplateController::class, 'preview'])->name('contract-template.preview');
        Route::post('contract-template/{template}/restore', [ContractTemplateController::class, 'restore'])->name('contract-template.restore');
        Route::post('orders/{order}/resend-email', [OrderController::class, 'resendEmail'])->name('orders.resend-email');

        // Achievements
        Route::resource('certifications', CertificationController::class);
        Route::resource('clients', ClientController::class);

        // Faq
        Route::resource('faqs', FaqController::class);

        // Staff/Team
        Route::resource('staff', StaffController::class);

        // Pages
        Route::resource('pages', PageController::class);

        // Settings
        Route::get('/settings', [SettingController::class, 'edit'])->name('settings.edit');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');

        // Tenant Configurations
        // Customer requests
        Route::get('account-requests', [AccountRequestController::class, 'index'])->name('account-requests.index');
        Route::put('account-requests/{accountRequest}', [AccountRequestController::class, 'update'])->name('account-requests.update');

        Route::resource('tenants', \App\Http\Controllers\Admin\TenantConfigController::class);

        // PROFILE
        Route::get('/profile', [AdminProfileController::class, 'getProfile'])->name('profile');
        Route::post('/profile/edit', [AdminProfileController::class, 'editProfile'])->name('profile.edit');
        Route::post('/profile/change-password', [AdminProfileController::class, 'changePassword'])->name('profile.password');

        Route::get('/logout', [AdminLoginController::class, 'logout'])->name('logout');
    });
});


Route::get('/storage-link', function () {
    Artisan::call('storage:link');
});

Route::get('/clear-cache', function () {
    Artisan::call('config:cache');
});

Route::get('/queue-restart', function () {
    Artisan::call('queue:restart');
});
