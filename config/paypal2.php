<?php

// Second PayPal account, used by tenants whose TenantConfig.payment_gateway is
// Order::PAYMENT_GATEWAY_PAYPAL2. Same shape as config/paypal.php. TenantResolver
// swaps this in over the 'paypal' config key for the current request when that
// gateway is resolved, so PayPalGateway itself needs no awareness of "account 2".
return [
    'mode'    => env('PAYPAL_MODE_2', 'sandbox'),
    'sandbox' => [
        'client_id'         => env('PAYPAL_SANDBOX_CLIENT_ID_2', ''),
        'client_secret'     => env('PAYPAL_SANDBOX_CLIENT_SECRET_2', ''),
        'app_id'            => env('PAYPAL_SANDBOX_APP_ID_2', 'APP-80W284485P519543T'),
    ],
    'live' => [
        'client_id'         => env('PAYPAL_LIVE_CLIENT_ID_2', ''),
        'client_secret'     => env('PAYPAL_LIVE_CLIENT_SECRET_2', ''),
        'app_id'            => env('PAYPAL_LIVE_APP_ID_2', ''),
    ],

    'payment_action' => env('PAYPAL_PAYMENT_ACTION_2', 'Sale'),
    'currency'       => env('PAYPAL_CURRENCY_2', 'USD'),
    'notify_url'     => env('PAYPAL_NOTIFY_URL_2', env('APP_URL') ? rtrim(env('APP_URL'), '/') . '/webhooks/paypal2' : ''),
    'locale'         => env('PAYPAL_LOCALE_2', 'en_US'),
    'validate_ssl'   => env('PAYPAL_VALIDATE_SSL_2', true),
    'webhook_id'     => env('PAYPAL_WEBHOOK_ID_2'),
];
