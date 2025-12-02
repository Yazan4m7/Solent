<?php

namespace Tests\Unit\Support;

use App\Modules\Contracts\Branding\BrandingRepositoryInterface;
use App\Modules\Contracts\Branding\BrandingResolverInterface;
use App\Support\Branding\BrandingManager;
use App\Support\Branding\BrandingSettings;
use Illuminate\Http\Request;
use Tests\TestCase;

class BrandingManagerTest extends TestCase
{
    public function test_it_resolves_and_caches_branding(): void
    {
        $repository = new class implements BrandingRepositoryInterface {
            public function forTenant(string $tenant): BrandingSettings
            {
                return new BrandingSettings(
                    $tenant,
                    'Demo Brand',
                    '/logo.png',
                    '/favicon.ico',
                    '#111111',
                    '#222222',
                    '#333333',
                    '#ffffff',
                    [],
                    []
                );
            }
        };

        $resolver = new class implements BrandingResolverInterface {
            public function resolveTenant(Request $request): string
            {
                return 'tenant-a';
            }
        };

        $manager = new BrandingManager($repository, $resolver, app('cache')->store('array'));
        $settings = $manager->current(new Request());

        $this->assertSame('tenant-a', $settings->tenant);
        $this->assertSame('#111111', $settings->primaryColor);

        // Cached result should be reused
        $this->assertSame($settings, $manager->current(new Request()));
    }
}
