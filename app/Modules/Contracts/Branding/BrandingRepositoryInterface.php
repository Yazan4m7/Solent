<?php

namespace App\Modules\Contracts\Branding;

use App\Support\Branding\BrandingSettings;

interface BrandingRepositoryInterface
{
    public function forTenant(string $tenant): BrandingSettings;
}
