<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Closure;
class Authenticate extends Middleware
{

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function handle($request, Closure $next, ...$guards)
    {
        //check here if the user is authenticated
        if ( ! $this->auth->user() || !Auth()->check() )
        {
            return redirect("/login");
        }

        return $next($request);
    }


    protected function redirectTo($request)
    {
        if (! $request->expectsJson() || !Auth()->check() ) {
            return redirect("/login");
        }
    }
}
