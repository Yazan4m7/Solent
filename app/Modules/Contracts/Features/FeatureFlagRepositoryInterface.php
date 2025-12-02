<?php

namespace App\Modules\Contracts\Features;

interface FeatureFlagRepositoryInterface
{
    /**
     * Returns true/false when a value is known, or null if the repository has no opinion.
     */
    public function enabled(string $feature, ?string $tenant = null): ?bool;
}
