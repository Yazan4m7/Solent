<?php

namespace App\Http\Middleware;

use Closure;

class AdminMiddleware
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

            if (Auth()->user()->is_admin) {

                return $next($request);

            }else{
                //return $next($request);
                return abort(403, "Insufficient Privileges, This action need an Administrator level.");

            }

        }else{

            return abort(403, "You're not logged in");

        }
    }
}
