<?php

namespace Tests\Unit\Tenancy;

use App\Support\Tenancy\TenantContext;
use Tests\TestCase;

class TenantContextTest extends TestCase
{
    public function test_normalize_host_trims_lowercases_and_removes_www_prefix(): void
    {
        $this->assertSame('lab.example.com', TenantContext::normalizeHost('  WWW.Lab.Example.COM  '));
        $this->assertSame('api.example.com', TenantContext::normalizeHost('api.example.com'));
        $this->assertSame('', TenantContext::normalizeHost(null));
    }

    public function test_from_array_accepts_legacy_aliases_and_normalizes_values(): void
    {
        $context = TenantContext::fromArray([
            'tenant_id' => '17',
            'tenant' => 'north-lab',
            'database_name' => 'north_lab_db',
            'tenant_name' => 'North Lab',
            'status' => ' ACTIVE ',
            'host' => 'WWW.NORTH.EXAMPLE.COM',
            'currency_code' => 'jod',
        ]);

        $this->assertSame(17, $context->tenantId);
        $this->assertSame('north-lab', $context->slug);
        $this->assertSame('north_lab_db', $context->database);
        $this->assertSame('North Lab', $context->name);
        $this->assertSame('active', $context->status);
        $this->assertSame('north.example.com', $context->domain);
        $this->assertSame('JOD', $context->currencyCode);
        $this->assertSame('north-lab', $context->brandingKey);
        $this->assertTrue($context->isResolved());
        $this->assertTrue($context->isActive());
    }

    public function test_unresolved_context_is_never_treated_as_resolved(): void
    {
        $context = TenantContext::unresolved('WWW.UNKNOWN.TEST', 'no tenant');

        $this->assertFalse($context->isResolved());
        $this->assertFalse($context->isActive());
        $this->assertSame('unknown.test', $context->domain);
        $this->assertSame('no tenant', $context->reason);
    }

    public function test_local_context_uses_configured_currency_and_branding_defaults(): void
    {
        config()->set('domain_context.default.currency_code', 'usd');
        config()->set('branding.default_tenant', 'solent-default');

        $context = TenantContext::local('WWW.LOCAL.TEST', 'local_db');

        $this->assertTrue($context->isLocal());
        $this->assertTrue($context->isResolved());
        $this->assertSame('USD', $context->currencyCode);
        $this->assertSame('solent-default', $context->brandingKey);
        $this->assertSame('local.test', $context->domain);
    }

    public function test_domain_context_preserves_defaults_but_overrides_tenant_specific_values(): void
    {
        config()->set('domain_context.default', [
            'country_code' => 'JO',
            'currency_code' => 'JOD',
            'custom' => 'keep-me',
        ]);

        $context = new TenantContext(
            7,
            'tenant-uuid',
            'tenant-slug',
            'Tenant Name',
            'tenant_db',
            'active',
            'tenant.test',
            'EUR',
            'tenant-brand'
        );

        $domain = $context->domainContext();

        $this->assertSame('keep-me', $domain['custom']);
        $this->assertSame(7, $domain['tenant_id']);
        $this->assertSame('tenant_db', $domain['database']);
        $this->assertSame('EUR', $domain['currency_code']);
        $this->assertTrue($domain['matched']);
    }

    public function test_cache_key_falls_back_to_slug_when_uuid_is_empty(): void
    {
        $context = new TenantContext(null, '', 'fallback-slug', 'Test', 'db', 'active', 'test.local');

        $this->assertSame('fallback-slug', $context->cacheKey());
    }
}
