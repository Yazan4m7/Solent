<?php

namespace Tests\Unit\Tenancy;

use App\Support\Tenancy\TenantContext;
use App\Support\Tenancy\TenantDatabaseManager;
use Tests\TestCase;

class TenantDatabaseManagerTest extends TestCase
{
    public function test_it_switches_only_the_reusable_tenant_connection(): void
    {
        $originalDefault = config('database.default');
        $originalLandlord = config('database.connections.landlord');
        $originalTenant = config('database.connections.tenant');

        try {
            config()->set('tenancy.tenant_connection', 'tenant');
            config()->set('tenancy.tenant_connection_template', 'mysql');
            config()->set('database.default', 'mysql');
            config()->set('database.connections.landlord.database', 'landlord_db');
            config()->set('database.connections.tenant.database', 'placeholder_db');

            $context = TenantContext::fromArray([
                'uuid' => 'tenant-a',
                'slug' => 'tenant-a',
                'database' => 'tenant_a_db',
                'status' => 'active',
            ]);

            (new TenantDatabaseManager())->connect($context, false);

            $this->assertSame('tenant', config('database.default'));
            $this->assertSame('tenant_a_db', config('database.connections.tenant.database'));
            $this->assertSame('landlord_db', config('database.connections.landlord.database'));
        } finally {
            config()->set('database.default', $originalDefault);
            config()->set('database.connections.landlord', $originalLandlord);
            config()->set('database.connections.tenant', $originalTenant);
        }
    }
}
