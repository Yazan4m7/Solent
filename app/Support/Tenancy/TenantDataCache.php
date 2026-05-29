<?php

namespace App\Support\Tenancy;

use Illuminate\Cache\Repository;
use Illuminate\Contracts\Cache\Repository as CacheRepository;

class TenantDataCache
{
    private CacheRepository $cache;
    private TenantContext $context;

    public function __construct(?CacheRepository $cache = null, ?TenantContext $context = null)
    {
        $this->cache = $cache ?? app('cache')->store();
        $this->context = $context ?? (app()->bound('app.tenant_context')
            ? app('app.tenant_context')
            : TenantContext::local(request()->getHost(), config('database.connections.' . config('database.default') . '.database')));
    }

    public function remember(string $scope, int $ttlSeconds, callable $callback, array $vary = [])
    {
        return $this->cache->remember(
            $this->key($scope, $vary),
            now()->addSeconds($ttlSeconds),
            $callback
        );
    }

    public function key(string $scope, array $vary = []): string
    {
        $tenantKey = $this->context->cacheKey();
        $normalized = $this->normalize($vary);

        return 'tenant_data:' . $tenantKey . ':' . $scope . ':' . sha1(json_encode($normalized));
    }

    private function normalize($value)
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->format(DATE_ATOM);
        }

        if (is_array($value)) {
            $isList = array_keys($value) === range(0, count($value) - 1);
            if (!$isList) {
                ksort($value);
            }

            foreach ($value as $key => $item) {
                $value[$key] = $this->normalize($item);
            }
        }

        return $value;
    }
}
