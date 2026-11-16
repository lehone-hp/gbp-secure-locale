<?php

return [

    'prefix' => env('INVOICE_PREFIX', 'FAC'),

    'company' => [
        'name'           => env('INVOICE_COMPANY_NAME', 'GBP Secure Local'),
        'tagline'        => env('INVOICE_COMPANY_TAGLINE', 'VOTRE VISIBILITÉ, NOTRE EXPERTISE'),
        'address_line_1' => env('INVOICE_ADDRESS_1', '8 Rue de Londres, 75009'),
        'address_line_2' => env('INVOICE_ADDRESS_2', 'Paris, France'),
        'country'        => env('INVOICE_COUNTRY', 'France'),
        'email'          => env('INVOICE_EMAIL', 'contact@gbp-securelocal.com'),
        'website'        => env('INVOICE_WEBSITE', 'www.gbp-securelocal.com'),
    ],

    'company_ca' => [
        'address_line_1' => env('INVOICE_CA_ADDRESS_1', '1442 Rue de l\'avenir'),
        'address_line_2' => env('INVOICE_CA_ADDRESS_2', 'Trois-Rivières, Québec'),
        'country'        => 'Canada',
    ],

    'tax' => [
        'rate'  => (float) env('INVOICE_TAX_RATE', 0.20),
        'label' => env('INVOICE_TAX_LABEL', 'TVA'),
    ],

];
