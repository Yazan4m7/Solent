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
        'name' => env('APP_NAME', 'Solent'),
        'logo_path' => env('BRANDING_LOGO', 'images/brands/solent/solent_v.svg'),
        'mark_path' => env('BRANDING_MARK', 'images/brands/solent/solent_h.svg'),
        'favicon_path' => env('BRANDING_FAVICON', 'images/brands/solent/solent_icon.svg'),
        // Palette stays aligned with the existing system colors
        'primary_color' => env('BRANDING_PRIMARY', '#c89b3c'),
        'secondary_color' => env('BRANDING_SECONDARY', '#e6c77a'),
        'accent_color' => env('BRANDING_ACCENT', '#0f141c'),
        'background_color' => env('BRANDING_BACKGROUND', '#0b1117'),
        'copy' => [
            'tagline' => 'Precision dental labs, refined.',
            'footer' => 'Powered by Korvion',
        ],
    ],
];
