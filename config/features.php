<?php

return [
    'cache_ttl' => env('FEATURE_FLAG_CACHE_TTL', 120),

    'dashboard' => [
        'sample_data' => env('DASHBOARD_SAMPLE_DATA', false),
    ],

    // Default flags keyed by tenant. "default" applies to all unless overridden.
    'flags' => [
        'default' => [
            'operations-dashboard-v2' => true,
            'employee-production-board-v2' => env('EMPLOYEE_PRODUCTION_BOARD_V2', true),
            'modules.abutments' => true,
            'modules.delivery' => true,
            'modules.media' => true,
        ],
    ],
];
