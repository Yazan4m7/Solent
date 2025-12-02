<?php

namespace App\Support\FeatureFlags\Repositories;

use App\Modules\Contracts\Features\FeatureFlagRepositoryInterface;

class ConfigFeatureFlagRepository implements FeatureFlagRepositoryInterface
{
    public function enabled(string $feature, ?string $tenant = null): ?bool
    {
        $flags = config('features.flags', []);
        $tenant = $tenant ?? 'default';

        if (isset($flags[$tenant]) && array_key_exists($feature, $flags[$tenant])) {
            return (bool) $flags[$tenant][$feature];
        }

        if (isset($flags['default']) && array_key_exists($feature, $flags['default'])) {
            return (bool) $flags['default'][$feature];
        }

        return null;
    }
}
