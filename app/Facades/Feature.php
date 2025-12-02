<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static bool enabled(string $feature, ?string $tenant = null)
 */
class Feature extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \App\Support\FeatureFlags\FeatureManager::class;
    }
}
