<?php

namespace Tests\Unit\Tenancy;

use App\Support\Tenancy\TenantContext;
use App\Support\Tenancy\TenantDataCache;
use Illuminate\Cache\ArrayStore;
use Illuminate\Cache\Repository;
use Tests\TestCase;

class TenantDataCacheTest extends TestCase
{
    public function test_it_caches_data_per_tenant_and_normalized_filters(): void
    {
        $repository = new Repository(new ArrayStore());
        $tenantA = TenantContext::fromArray([
            'uuid' => 'tenant-a',
            'slug' => 'a',
            'database' => 'tenant_a',
            'status' => 'active',
        ]);
        $tenantB = TenantContext::fromArray([
            'uuid' => 'tenant-b',
            'slug' => 'b',
            'database' => 'tenant_b',
            'status' => 'active',
        ]);

        $cacheA = new TenantDataCache($repository, $tenantA);
        $cacheB = new TenantDataCache($repository, $tenantB);
        $calls = 0;

        $first = $cacheA->remember('dashboard.cards', 60, function () use (&$calls) {
            $calls++;

            return ['gross_margin' => 75];
        }, ['filters' => ['b' => 2, 'a' => 1], 'user_id' => 5]);

        $second = $cacheA->remember('dashboard.cards', 60, function () use (&$calls) {
            $calls++;

            return ['gross_margin' => 90];
        }, ['user_id' => 5, 'filters' => ['a' => 1, 'b' => 2]]);

        $third = $cacheB->remember('dashboard.cards', 60, function () use (&$calls) {
            $calls++;

            return ['gross_margin' => 88];
        }, ['filters' => ['a' => 1, 'b' => 2], 'user_id' => 5]);

        $this->assertSame(['gross_margin' => 75], $first);
        $this->assertSame(['gross_margin' => 75], $second);
        $this->assertSame(['gross_margin' => 88], $third);
        $this->assertSame(2, $calls);
    }
}
