<?php

namespace Tests\Unit\Tenancy;

use App\Support\Tenancy\TenantContext;
use App\Support\Tenancy\TenantStorage;
use Tests\TestCase;

class TenantStorageTest extends TestCase
{
    public function test_it_prefixes_new_paths_with_the_tenant_uuid(): void
    {
        $context = TenantContext::fromArray([
            'uuid' => 'tenant-uuid',
            'slug' => 'demo',
            'database' => 'tenant_demo',
            'status' => 'active',
        ]);
        $storage = new TenantStorage($context);

        $this->assertSame(
            'tenants/tenant-uuid/caseImages/42/scan.png',
            $storage->path('/caseImages/42/scan.png')
        );
    }

    public function test_existing_tenant_prefixed_paths_are_not_double_prefixed(): void
    {
        $context = TenantContext::fromArray([
            'uuid' => 'tenant-uuid',
            'slug' => 'demo',
            'database' => 'tenant_demo',
            'status' => 'active',
        ]);
        $storage = new TenantStorage($context);

        $this->assertSame(
            'tenants/tenant-uuid/caseImages/42/scan.png',
            $storage->path('tenants/tenant-uuid/caseImages/42/scan.png')
        );
    }
}
