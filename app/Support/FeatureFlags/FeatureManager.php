<?php

namespace App\Support\FeatureFlags;

use App\Modules\Contracts\Features\FeatureFlagRepositoryInterface;
use Illuminate\Contracts\Cache\Repository as CacheRepository;

class FeatureManager
{
    /**
     * @var FeatureFlagRepositoryInterface[]
     */
    private array $repositories;
    private CacheRepository $cache;
    private int $cacheTtl;

    /**
     * @param iterable<FeatureFlagRepositoryInterface> $repositories
     */
    public function __construct(iterable $repositories, CacheRepository $cache)
    {
        $this->repositories = is_array($repositories) ? $repositories : iterator_to_array($repositories);
        $this->cache = $cache;
        $this->cacheTtl = (int) config('features.cache_ttl', 120);
    }

    public function enabled(string $feature, ?string $tenant = null): bool
    {
        $tenant = $tenant ?? 'default';
        $cacheKey = 'feature:' . $tenant . ':' . $feature;

        return $this->cache->remember($cacheKey, $this->cacheTtl, function () use ($feature, $tenant): bool {
            foreach ($this->repositories as $repository) {
                $value = $repository->enabled($feature, $tenant);
                if ($value !== null) {
                    return (bool) $value;
                }
            }

            return false;
        });
    }
}
