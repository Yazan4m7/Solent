<?php

namespace App\Modules\Financing\Http\Middleware;

use Closure;

class FinancingAccess
{
    public function handle($request, Closure $next)
    {
        $user = auth()->user();

        if (! $user) {
            abort(403, "You're not logged in");
        }

        if ((bool) ($user->is_admin ?? false)) {
            return $next($request);
        }

        $permissionIds = array_map('intval', (array) config('modules.financing.accountant_permission_ids', [121]));

        if (method_exists($user, 'permissions')
            && $user->permissions()->whereIn('permission_id', $permissionIds)->exists()) {
            return $next($request);
        }

        abort(403, 'Insufficient privileges for the financing module.');
    }
}
