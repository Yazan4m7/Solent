<?php

namespace App\Support\Tenancy;

use App\Support\DemoMode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/** A separate, provisioned sample database; never a copy of a customer's records. */
class TenantDemoDatabase
{
    public const CONNECTION = 'tenant_demo';
    public const MARKER = 'tenant_demo_identity';
    public const PREFERENCE_VERSION = 1;

    public function supports(Request $request, ?TenantContext $context): bool
    {
        return $context && $context->isResolved() && $context->isActive()
            && ! DemoMode::isDemoRequest($request)
            && $request->getHost() !== config('tenancy.platform_admin_host')
            && $context->database !== config('domain_context.demo.database');
    }

    public function databaseName(TenantContext $context): string
    {
        return 'solent_preview_' . substr(hash('sha256', $context->uuid . ':' . $context->database), 0, 20);
    }

    public function sessionKey(TenantContext $context, $user): string
    {
        return 'tenant_demo.' . hash('sha256', $context->domain . ':' . $context->uuid . ':' . $user->getAuthIdentifier());
    }

    public function connection(TenantContext $context)
    {
        $configuration = DB::connection()->getConfig();
        $configuration['url'] = null;
        $configuration['database'] = $this->databaseName($context);
        // A configured read replica must not redirect sample reads to a real database.
        unset($configuration['read'], $configuration['write'], $configuration['name']);
        config(['database.connections.' . self::CONNECTION => $configuration]);
        DB::purge(self::CONNECTION);

        return DB::connection(self::CONNECTION);
    }

    public function verify(TenantContext $context)
    {
        $connection = $this->connection($context);
        $identity = $connection->table(self::MARKER)->first();
        if (! $identity || $identity->tenant_uuid !== $context->uuid
            || $identity->real_database !== $context->database
            || ! $connection->table('cases')->exists()) {
            throw new RuntimeException('The sample database is not ready for this website.');
        }

        return $connection;
    }

    public function activate(TenantContext $real): void
    {
        $connection = $this->verify($real);
        // Defense in depth for legacy GET handlers which unexpectedly write.
        if ($connection->getDriverName() === 'mysql') {
            $connection->statement('SET SESSION TRANSACTION READ ONLY');
        } elseif ($connection->getDriverName() === 'sqlite') {
            $connection->statement('PRAGMA query_only = ON');
        }

        $demo = clone $real;
        $demo->database = $this->databaseName($real);
        $demo->uuid .= '-demo';
        $demo->source = 'tenant_demo';
        app()->instance('app.tenant_context', $demo);
        app()->instance('app.tenant_demo_active', true);
        DB::setDefaultConnection(self::CONNECTION);
    }
}
