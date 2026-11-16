<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Payment Gateway
    |--------------------------------------------------------------------------
    |
    | This option controls the default payment gateway that will be used
    | for processing payments. You may set this to any of the gateways
    | defined in the "gateways" array below.
    |
    */

    'default_gateway' => env('PAYMENT_GATEWAY', 'braintree'),

    /*
    |--------------------------------------------------------------------------
    | Custom Payment Links
    |--------------------------------------------------------------------------
    |
    | When true, add-ons that have a custom payment link send the buyer to
    | that link. Add-ons without one, or all add-ons when this is false,
    | use the normal checkout flow.
    |
    */

    'use_custom_links' => env('USE_CUSTOM_PAYMENT_LINKS', false),

    /*
    |--------------------------------------------------------------------------
    | Tenant Mode
    |--------------------------------------------------------------------------
    |
    | Enable multitenancy support for domain-based payment gateway configuration.
    | When enabled, the system will check for tenant-specific configurations
    | in the database before falling back to the default gateway.
    |
    */

    'tenant_mode_enabled' => env('TENANT_MODE_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | Tenant Cache TTL
    |--------------------------------------------------------------------------
    |
    | Time-to-live (in seconds) for cached tenant configurations.
    | Default is 3600 seconds (1 hour).
    |
    */

    'tenant_cache_ttl' => env('TENANT_CACHE_TTL', 3600),

    /*
    |--------------------------------------------------------------------------
    | Payment Gateways
    |--------------------------------------------------------------------------
    |
    | Here you may configure the payment gateways that your application
    | supports. Each gateway has its own configuration options.
    | These serve as default configurations when tenant mode is disabled
    | or when no tenant configuration is found.
    |
    */

    'gateways' => [

        'braintree' => [
            'currencies' => explode(',', env('BRAINTREE_CURRENCIES', 'USD,CAD,EUR')),
            'environment' => env('BRAINTREE_ENVIRONMENT', 'sandbox'),
            'merchant_id' => env('BRAINTREE_MERCHANT_ID'),
            'public_key' => env('BRAINTREE_PUBLIC_KEY'),
            'private_key' => env('BRAINTREE_PRIVATE_KEY'),
        ],

        'stripe' => [
            'currencies' => explode(',', env('STRIPE_CURRENCIES', 'USD,CAD,EUR')),
            'key' => env('STRIPE_KEY'),
            'secret' => env('STRIPE_SECRET'),
            'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
        ],

        'paypal' => [
            'currencies' => explode(',', env('PAYPAL_CURRENCIES', 'USD,CAD,EUR')),
            'mode' => env('PAYPAL_MODE', 'sandbox'),
            'client_id' => env('PAYPAL_CLIENT_ID'),
            'secret' => env('PAYPAL_SECRET'),
            'webhook_id' => env('PAYPAL_WEBHOOK_ID'),
        ],

        'payunit' => [
            'currencies' => explode(',', env('PAYUNIT_CURRENCIES', 'USD')),
            'api_key' => env('PAYUNIT_API_KEY'),
            'api_username' => env('PAYUNIT_API_USERNAME'),
            'api_password' => env('PAYUNIT_API_PASSWORD'),
            'mode' => env('PAYUNIT_MODE', 'sandbox'),
        ],

        'square' => [
            'currencies' => [env('SQUARE_CURRENCY', 'EUR')],
            'application_id' => env('SQUARE_APPLICATION_ID'),
            'access_token'   => env('SQUARE_ACCESS_TOKEN'),
            'location_id'    => env('SQUARE_LOCATION_ID'),
            'webhook_secret' => env('SQUARE_WEBHOOK_SIGNATURE_KEY'),
            'environment'    => env('SQUARE_ENVIRONMENT', 'sandbox'),
            'currency'       => env('SQUARE_CURRENCY', 'EUR'),
        ],

        'whop' => [
            'currencies' => explode(',', env('WHOP_CURRENCIES', 'USD,CAD,EUR')),
            'environment'    => env('WHOP_ENVIRONMENT', 'sandbox'),
            'api_key'        => env('WHOP_API_KEY'),
            'company_id'     => env('WHOP_COMPANY_ID'),
            'webhook_secret' => env('WHOP_WEBHOOK_SECRET'),
        ],

        'mollie' => [
            'currencies' => explode(',', env('MOLLIE_CURRENCIES', 'USD,CAD,EUR')),
            'api_key'    => env('MOLLIE_API_KEY'),
        ],

    ],

];
