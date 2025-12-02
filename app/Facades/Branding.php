<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \App\Support\Branding\BrandingSettings current(\Illuminate\Http\Request $request = null)
 * @method static \App\Support\Branding\BrandingSettings forTenant(string $tenant)
 */
class Branding extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \App\Support\Branding\BrandingManager::class;
    }
}
