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
        $user = $this->auth->user();
        if (! $user || ! Auth()->check())
        {
            return redirect("/login");
        }

        $attributes = method_exists($user, 'getAttributes') ? $user->getAttributes() : [];
        $isDisabled =
            (array_key_exists('status', $attributes) && ! (bool) $user->status) ||
            (array_key_exists('active', $attributes) && ! (bool) $user->active);

        if ($isDisabled) {
            $this->auth->logout();

            if ($request->hasSession()) {
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }

            return redirect('/login')->with('error', 'This account is disabled.');
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
