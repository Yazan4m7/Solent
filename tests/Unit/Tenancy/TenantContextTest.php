<?php

namespace Tests\Unit\Tenancy;

use App\Support\Tenancy\TenantContext;
use Tests\TestCase;

class TenantContextTest extends TestCase
{
    public function test_it_normalizes_active_tenant_context(): void
    {
        $context = TenantContext::fromArray([
            'tenant_id' => 10,
            'uuid' => 'tenant-uuid',
            'slug' => 'lab-a',
            'name' => 'Lab A',
            'database' => 'tenant_lab_a',
            'status' => 'ACTIVE',
            'domain' => 'Lab-A.Example.test',
            'currency_code' => 'jod',
            'branding_key' => 'brand-a',
        ]);

        $this->assertTrue($context->isResolved());
        $this->assertTrue($context->isActive());
        $this->assertSame('active', $context->status);
        $this->assertSame('lab-a.example.test', $context->domain);
        $this->assertSame('JOD', $context->currencyCode);
        $this->assertSame('brand-a', $context->brandingKey);
    }

    public function test_local_context_is_resolved_without_a_tenant_database_switch(): void
    {
        $context = TenantContext::local('127.0.0.1', 'app_local');

        $this->assertTrue($context->isResolved());
        $this->assertTrue($context->isActive());
        $this->assertTrue($context->isLocal());
        $this->assertSame('local', $context->uuid);
        $this->assertSame('app_local', $context->database);
    }
}
