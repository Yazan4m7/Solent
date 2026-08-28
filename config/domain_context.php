<?php

return [
    'default' => [
        'country_code' => env('APP_COUNTRY_CODE', 'JO'),
        'country_name' => env('APP_COUNTRY_NAME', 'Jordan'),
        'database' => env('DB_DATABASE', 'forge'),
        'currency_code' => env('APP_CURRENCY_CODE', 'JOD'),
        'currency_display' => env('APP_CURRENCY_DISPLAY', env('APP_CURRENCY_CODE', 'JOD')),
        'currency_symbol' => env('APP_CURRENCY_SYMBOL', 'JOD'),
        'currency_unit_ar' => env('APP_CURRENCY_UNIT_AR', 'دينار'),
        'currency_name_ar' => env('APP_CURRENCY_NAME_AR', 'دينار أردني'),
        'currency_name_en' => env('APP_CURRENCY_NAME_EN', 'Jordanian Dinar'),
    ],

    'hosts' => [
        'demo.solentjo.com' => [
            'country_code' => 'DEMO',
            'country_name' => 'Demo',
            'database' => env('DEMO_DB_DATABASE'),
            'currency_code' => env('DEMO_APP_CURRENCY_CODE', 'JOD'),
            'currency_display' => env('DEMO_APP_CURRENCY_DISPLAY', env('DEMO_APP_CURRENCY_CODE', 'JOD')),
            'currency_symbol' => env('DEMO_APP_CURRENCY_SYMBOL', 'JOD'),
            'currency_unit_ar' => env('DEMO_APP_CURRENCY_UNIT_AR', 'دينار'),
            'currency_name_ar' => env('DEMO_APP_CURRENCY_NAME_AR', 'دينار أردني'),
            'currency_name_en' => env('DEMO_APP_CURRENCY_NAME_EN', 'Jordanian Dinar'),
        ],
        'demo.ceralis.com' => [
            'country_code' => 'DEMO',
            'country_name' => 'Demo',
            'database' => env('DEMO_DB_DATABASE'),
            'currency_code' => env('DEMO_APP_CURRENCY_CODE', 'JOD'),
            'currency_display' => env('DEMO_APP_CURRENCY_DISPLAY', env('DEMO_APP_CURRENCY_CODE', 'JOD')),
            'currency_symbol' => env('DEMO_APP_CURRENCY_SYMBOL', 'JOD'),
            'currency_unit_ar' => env('DEMO_APP_CURRENCY_UNIT_AR', 'دينار'),
            'currency_name_ar' => env('DEMO_APP_CURRENCY_NAME_AR', 'دينار أردني'),
            'currency_name_en' => env('DEMO_APP_CURRENCY_NAME_EN', 'Jordanian Dinar'),
        ],
        'demo.ceralith.com' => [
            'country_code' => 'DEMO',
            'country_name' => 'Demo',
            'database' => env('DEMO_DB_DATABASE'),
            'currency_code' => env('DEMO_APP_CURRENCY_CODE', 'JOD'),
            'currency_display' => env('DEMO_APP_CURRENCY_DISPLAY', env('DEMO_APP_CURRENCY_CODE', 'JOD')),
            'currency_symbol' => env('DEMO_APP_CURRENCY_SYMBOL', 'JOD'),
            'currency_unit_ar' => env('DEMO_APP_CURRENCY_UNIT_AR', 'دينار'),
            'currency_name_ar' => env('DEMO_APP_CURRENCY_NAME_AR', 'دينار أردني'),
            'currency_name_en' => env('DEMO_APP_CURRENCY_NAME_EN', 'Jordanian Dinar'),
        ],
        'orva.korviongroup.com' => [
            'country_code' => 'DEMO',
            'country_name' => 'Orva',
            'database' => env('DEMO_DB_DATABASE'),
            'currency_code' => env('DEMO_APP_CURRENCY_CODE', 'JOD'),
            'currency_display' => env('DEMO_APP_CURRENCY_DISPLAY', env('DEMO_APP_CURRENCY_CODE', 'JOD')),
            'currency_symbol' => env('DEMO_APP_CURRENCY_SYMBOL', 'JOD'),
            'currency_unit_ar' => env('DEMO_APP_CURRENCY_UNIT_AR', 'دينار'),
            'currency_name_ar' => env('DEMO_APP_CURRENCY_NAME_AR', 'دينار أردني'),
            'currency_name_en' => env('DEMO_APP_CURRENCY_NAME_EN', 'Jordanian Dinar'),
        ],
    ],

    'selection_domains' => [],

    'selection_host' => env('DOMAIN_SELECTION_HOST', 'solentjo.com'),

    'selection_cookie' => [
        'name' => env('DOMAIN_SELECTION_COOKIE', 'lab_country_domain'),
        'minutes' => env('DOMAIN_SELECTION_COOKIE_MINUTES', 525600),
        'domain' => env('DOMAIN_SELECTION_COOKIE_DOMAIN', '.solentjo.com'),
    ],

    'demo' => [
        'enabled' => env('DEMO_MODE_ENABLED', true),
        'read_only' => env('DEMO_READ_ONLY', false),
        'database' => env('DEMO_DB_DATABASE'),
        'hosts' => array_filter([
            'demo.solentjo.com',
            env('DEMO_HOST', 'demo.ceralis.com'),
            env('DEMO_ALT_HOST', 'demo.ceralith.com'),
            env('DEMO_ORVA_HOST', 'orva.korviongroup.com'),
        ]),
        'user' => [
            'username' => env('DEMO_USER_USERNAME', 'demo'),
            'email' => env('DEMO_USER_EMAIL', 'demo@ceralith.local'),
            'password' => env('DEMO_USER_PASSWORD', 'demo'),
            'first_name' => env('DEMO_USER_FIRST_NAME', 'Demo'),
            'last_name' => env('DEMO_USER_LAST_NAME', 'User'),
            'initials' => env('DEMO_USER_INITIALS', 'DU'),
            'name' => env('DEMO_USER_NAME', 'Demo User'),
            'phone' => env('DEMO_USER_PHONE', '0000000000'),
        ],
    ],
];
