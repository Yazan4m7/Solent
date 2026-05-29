<?php

namespace App\Support\Tenancy;

use Illuminate\Support\Facades\DB;

class TenantDatabaseManager
{
    public function connect(TenantContext $context, bool $reconnect = true): void
    {
        if (!$context->isResolved() || !$context->isActive() || $context->isLocal()) {
            return;
        }

        $tenantConnection = (string) config('tenancy.tenant_connection', 'tenant');

        config()->set('database.connections.' . $tenantConnection, $this->buildTenantConnectionConfig($context));
        config()->set('database.default', $tenantConnection);

        DB::purge($tenantConnection);

        if ($reconnect) {
            DB::reconnect($tenantConnection);
        }
    }

    public function buildTenantConnectionConfig(TenantContext $context): array
    {
        $templateConnection = (string) config('tenancy.tenant_connection_template', 'mysql');
        $tenantConnection = (string) config('tenancy.tenant_connection', 'tenant');
        $template = (array) config('database.connections.' . $templateConnection, []);
        $tenant = (array) config('database.connections.' . $tenantConnection, []);

        if (count($template) === 0) {
            $template = (array) config('database.connections.mysql', []);
        }

        $connection = array_merge($template, array_filter($tenant, function ($value) {
            return $value !== null;
        }));

        $connection['database'] = $context->database;

        return $connection;
    }
}
