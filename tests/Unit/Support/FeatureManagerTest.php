<?php

namespace Tests\Unit\Support;

use App\Modules\Contracts\Features\FeatureFlagRepositoryInterface;
use App\Support\FeatureFlags\FeatureManager;
use Tests\TestCase;

class FeatureManagerTest extends TestCase
{
    public function test_it_checks_repositories_in_order(): void
    {
        $primary = new class implements FeatureFlagRepositoryInterface {
            public function enabled(string $feature, ?string $tenant = null): ?bool
            {
                return $feature === 'primary-only' ? true : null;
            }
        };

        $fallback = new class implements FeatureFlagRepositoryInterface {
            public function enabled(string $feature, ?string $tenant = null): ?bool
            {
                return $feature === 'fallback-flag' ? false : null;
            }
        };

        $manager = new FeatureManager([$primary, $fallback], app('cache')->store('array'));

        $this->assertTrue($manager->enabled('primary-only'));
        $this->assertFalse($manager->enabled('fallback-flag'));
        $this->assertFalse($manager->enabled('unknown-flag'));
    }
}
