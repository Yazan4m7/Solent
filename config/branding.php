<?php

return [
    'default_tenant' => env('APP_TENANT', 'default'),
    'cache_ttl' => env('BRANDING_CACHE_TTL', 300),
    'resolver' => [
        'header' => env('BRANDING_HEADER', 'X-Tenant'),
        'query' => env('BRANDING_QUERY', 'tenant'),
        'host_map' => [
            // 'tenant.example.com' => 'tenant-key',
        ],
    ],
    'defaults' => [
        'name' => env('APP_NAME', 'Korvion'),
        'logo_path' => env('BRANDING_LOGO', 'assets/images/Logo/square_logo.png'),
        'favicon_path' => env('BRANDING_FAVICON', 'assets/images/Logo/square_logo.png'),
        // Korvion palette inspired by the provided logo
        'primary_color' => env('BRANDING_PRIMARY', '#c89b3c'),       // gold
        'secondary_color' => env('BRANDING_SECONDARY', '#e6c77a'),   // soft gold highlight
        'accent_color' => env('BRANDING_ACCENT', '#0f141c'),         // deep charcoal/navy
        'background_color' => env('BRANDING_BACKGROUND', '#0b1117'), // dark background
        'copy' => [
            'tagline' => 'Precision dental labs, refined.',
            'footer' => 'Powered by Korvion',
        ],
    ],
];
