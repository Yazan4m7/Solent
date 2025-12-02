<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;

class OrderAbutmentMiddleware
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
            if (Auth()->user()->is_admin || $permissions->contains('permission_id', 126)) {
                return $next($request);
            }else{
                return abort(403, "Insufficient Privileges, You don't have the permission to view receive abutments from outside of lab, Contact Admin");
            }
        }else{

            return abort(403, "You're not logged in");

        }
    }
}
