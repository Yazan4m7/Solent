<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Existing SIGMA model classes
    |--------------------------------------------------------------------------
    | Change only these values if your existing models live under App\Models.
    */
    'models' => [
        'user' => env('FINANCING_USER_MODEL', 'App\\User'),
        'client' => env('FINANCING_CLIENT_MODEL', 'App\\client'),
        'invoice' => env('FINANCING_INVOICE_MODEL', 'App\\invoice'),
        'payment' => env('FINANCING_PAYMENT_MODEL', 'App\\payment'),
        'job' => env('FINANCING_JOB_MODEL', 'App\\job'),
    ],

    'currency' => env('FINANCING_CURRENCY', 'JOD'),
    'module_key' => 'module_financing',
    'accountant_permission_ids' => [121],
];
