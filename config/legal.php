<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Legal document versioning
    |--------------------------------------------------------------------------
    |
    | The refund policy version is stored on every order together with the
    | customer's consent, so bump it whenever the policy text changes.
    |
    */

    'refund_version' => env('LEGAL_REFUND_VERSION', '1.0'),

    'effective_date' => env('LEGAL_EFFECTIVE_DATE', '14 November 2026'),

];
