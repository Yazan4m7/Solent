<?php

return [
    'landlord_connection' => env('LANDLORD_DB_CONNECTION', 'landlord'),
    'tenant_connection' => env('TENANT_DB_CONNECTION', 'tenant'),
    'tenant_connection_template' => env('TENANT_DB_TEMPLATE_CONNECTION', 'mysql'),

    'local_override' => [
        'query' => env('TENANT_LOCAL_OVERRIDE_QUERY', 'tenant_domain'),
        'header' => env('TENANT_LOCAL_OVERRIDE_HEADER', 'X-Tenant-Domain'),
    ],

    'cache_ttls' => [
        'dashboard' => env('TENANT_CACHE_DASHBOARD_TTL', 60),
        'operations' => env('TENANT_CACHE_OPERATIONS_TTL', 30),
        'case_list' => env('TENANT_CACHE_CASE_LIST_TTL', 30),
    ],

    'platform_admin_host' => env('PLATFORM_ADMIN_HOST', 'admin.solentjo.com'),
    'platform_admin_connection' => 'platform_admin',
    'platform_admin_database' => env('PLATFORM_ADMIN_DB_DATABASE', env('LANDLORD_DB_DATABASE', env('DB_DATABASE', 'forge'))),
    'platform_admin_emails' => array_filter(array_map('trim', explode(',', env('PLATFORM_ADMIN_EMAILS', '')))),

    'provisioning' => [
        'database_prefix' => env('TENANT_DATABASE_PREFIX', ''),
        'database_suffix' => env('TENANT_DATABASE_SUFFIX', '_kordent'),
        'default_domain_suffix' => env('TENANT_DEFAULT_DOMAIN_SUFFIX', ''),
        'base_schema_path' => env('TENANT_BASE_SCHEMA_PATH', base_path('database_schema.sql')),
        'base_schema_migrations' => [
            '0001_01_01_000000_create_users_table',
            '0001_01_01_000002_create_jobs_table',
            '2019_12_14_000001_create_personal_access_tokens_table',
        ],
        'run_migrations' => env('TENANT_PROVISION_RUN_MIGRATIONS', true),
    ],
];
