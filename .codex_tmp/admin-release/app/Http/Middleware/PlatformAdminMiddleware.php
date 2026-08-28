<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlatformAdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if (!$user) {
            abort(403);
        }

        $allowedEmails = (array) config('tenancy.platform_admin_emails', []);

        if (($user->is_admin ?? false) && count($allowedEmails) === 0 && app()->environment(['local', 'testing'])) {
            return $next($request);
        }

        if (in_array(strtolower((string) $user->email), array_map('strtolower', $allowedEmails), true)) {
            return $next($request);
        }

        abort(403);
    }
}
