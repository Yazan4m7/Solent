<?php

namespace App\Http\Middleware;

use App\Events\BrandingResolved;
use App\Support\Branding\BrandingManager;
use App\Support\Branding\BrandingSettings;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class ApplyBranding
{
    public function handle(Request $request, Closure $next)
    {
        $settings = app(BrandingManager::class)->current($request);

        // Expose the current branding to the container and views for reuse.
        app()->instance(BrandingSettings::class, $settings);
        View::share('branding', $settings);
        event(new BrandingResolved($settings));

        return $next($request);
    }
}
