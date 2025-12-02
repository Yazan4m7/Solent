<?php

namespace App\Modules\Contracts\Branding;

use Illuminate\Http\Request;

interface BrandingResolverInterface
{
    public function resolveTenant(Request $request): string;
}
