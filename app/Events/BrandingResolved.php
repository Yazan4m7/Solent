<?php

namespace App\Events;

use App\Support\Branding\BrandingSettings;

class BrandingResolved
{
    public BrandingSettings $settings;

    public function __construct(BrandingSettings $settings)
    {
        $this->settings = $settings;
    }
}
