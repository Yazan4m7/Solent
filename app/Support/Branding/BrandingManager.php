<?php

namespace App\Support\Branding;

use App\Modules\Contracts\Branding\BrandingRepositoryInterface;
use App\Modules\Contracts\Branding\BrandingResolverInterface;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Http\Request;

class BrandingManager
{
    private BrandingRepositoryInterface $repository;
    private BrandingResolverInterface $resolver;
    private CacheRepository $cache;
    private int $cacheTtl;
    private ?BrandingSettings $current = null;

    public function __construct(
        BrandingRepositoryInterface $repository,
        BrandingResolverInterface $resolver,
        CacheRepository $cache
    ) {
        $this->repository = $repository;
        $this->resolver = $resolver;
        $this->cache = $cache;
        $this->cacheTtl = (int) config('branding.cache_ttl', 300);
    }

    public function current(?Request $request = null): BrandingSettings
    {
        if ($this->current) {
            return $this->current;
        }

        $tenant = $request
            ? $this->resolver->resolveTenant($request)
            : config('branding.default_tenant', 'default');

        $this->current = $this->forTenant($tenant);

        return $this->current;
    }

    public function forTenant(string $tenant): BrandingSettings
    {
        $cacheKey = 'branding:' . $tenant;

        return $this->cache->remember($cacheKey, $this->cacheTtl, function () use ($tenant): BrandingSettings {
            return $this->repository->forTenant($tenant);
        });
    }

    public function setCurrent(BrandingSettings $settings): void
    {
        $this->current = $settings;
    }
}
