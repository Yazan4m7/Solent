<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;

class EditCasesMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth()->check()) {
            $permissions = Cache::get('user'.Auth()->user()->id);
            if (Auth()->user()->is_admin || ($permissions->contains('permission_id', 102)|| $permissions->contains('permission_id', 1) ||$permissions->contains('permission_id', 115) )) {
                return $next($request);
            }else{
                return abort(403, "Insufficient Privileges, You don't have permission to edit cases at this stage, Contact Administrator");
            }
        }else{

            return abort(403, "You're not logged in");

        }
    }
}
