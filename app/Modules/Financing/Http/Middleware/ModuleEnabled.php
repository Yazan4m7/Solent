<?php

namespace App\Modules\Financing\Http\Middleware;

use Closure;

class ModuleEnabled
{
    public function handle($request, Closure $next, $module)
    {
        if ((string) setting('module_' . $module, '0') !== '1') {
            abort(403, 'This module is disabled.');
        }

        return $next($request);
    }
}
