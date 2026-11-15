<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Display and charge currencies
    |--------------------------------------------------------------------------
    |
    | All catalogue prices are stored in the base currency (USD). Rates are the
    | number of units of each currency per 1 USD and can be overridden from the
    | admin settings (rate_cad, rate_eur), which take precedence over these
    | defaults.
    |
    */

    'base' => 'USD',

    'default' => env('DEFAULT_CURRENCY', 'USD'),

    'supported' => [
        'USD' => ['symbol' => '$', 'label' => 'USD'],
        'CAD' => ['symbol' => 'CA$', 'label' => 'CAD'],
        'EUR' => ['symbol' => '€', 'label' => 'EUR'],
    ],

    'rates' => [
        'USD' => 1.0,
        'CAD' => (float) env('RATE_CAD', 1.37),
        'EUR' => (float) env('RATE_EUR', 0.92),
    ],

];
