<?php

namespace App\Support\FeatureFlags\Repositories;

use App\Modules\Contracts\Features\FeatureFlagRepositoryInterface;
use Illuminate\Support\Facades\Schema;
use JustSteveKing\Laravel\FeatureFlags\Models\Feature;

class DatabaseFeatureFlagRepository implements FeatureFlagRepositoryInterface
{
    public function enabled(string $feature, ?string $tenant = null): ?bool
    {
        if (! Schema::hasTable('features')) {
            return null;
        }

        $model = Feature::query()->where('name', $feature)->first();

        return $model ? (bool) $model->active : null;
    }
}
