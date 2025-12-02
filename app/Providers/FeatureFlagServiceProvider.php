<?php

namespace App\Providers;

use App\Support\FeatureFlags\FeatureManager;
use App\Support\FeatureFlags\Repositories\ConfigFeatureFlagRepository;
use App\Support\FeatureFlags\Repositories\DatabaseFeatureFlagRepository;
use Illuminate\Support\ServiceProvider;

class FeatureFlagServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ConfigFeatureFlagRepository::class);
        $this->app->singleton(DatabaseFeatureFlagRepository::class);

        $this->app->singleton(FeatureManager::class, function ($app): FeatureManager {
            return new FeatureManager(
                [
                    $app->make(DatabaseFeatureFlagRepository::class),
                    $app->make(ConfigFeatureFlagRepository::class),
                ],
                $app->make('cache')->store()
            );
        });
    }
}
