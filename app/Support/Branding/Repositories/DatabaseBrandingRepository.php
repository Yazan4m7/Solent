<?php

namespace App\Support\Branding\Repositories;

use App\Models\BrandSetting;
use App\Modules\Contracts\Branding\BrandingRepositoryInterface;
use App\Support\Branding\BrandingSettings;
use Illuminate\Support\Facades\Schema;

class DatabaseBrandingRepository implements BrandingRepositoryInterface
{
    public function forTenant(string $tenant): BrandingSettings
    {
        if (! Schema::hasTable('brand_settings')) {
            return BrandingSettings::fromConfig($tenant);
        }

        $setting = BrandSetting::query()->where('tenant', $tenant)->first();

        if (! $setting) {
            return BrandingSettings::fromConfig($tenant);
        }

        $defaults = config('branding.defaults', []);

        return new BrandingSettings(
            $tenant,
            $setting->name ?? ($defaults['name'] ?? 'App'),
            $setting->logo_path ?? ($defaults['logo_path'] ?? null),
            $setting->favicon_path ?? ($defaults['favicon_path'] ?? null),
            $setting->primary_color ?? ($defaults['primary_color'] ?? '#000000'),
            $setting->secondary_color ?? ($defaults['secondary_color'] ?? '#000000'),
            $setting->accent_color ?? ($defaults['accent_color'] ?? '#000000'),
            $setting->background_color ?? ($defaults['background_color'] ?? '#ffffff'),
            $setting->copy ?? ($defaults['copy'] ?? []),
            $setting->extra ?? []
        );
    }
}
