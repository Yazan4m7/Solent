<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AuthenticateClient
{
    public function handle($request, Closure $next)
    {
        $guard = Auth::guard('clients');
        $client = $guard->user();

        if (!$client) {
            return redirect('/portal/login');
        }

        $attributes = method_exists($client, 'getAttributes') ? $client->getAttributes() : [];
        if (array_key_exists('active', $attributes) && !(bool) $client->active) {
            $guard->logout();

            if ($request->hasSession()) {
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }

            return redirect('/portal/login')->with('error', 'This account is disabled.');
        }

        return $next($request);
    }
}
