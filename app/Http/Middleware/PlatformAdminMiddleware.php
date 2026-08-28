<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlatformAdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $adminHost = $this->normalizeHost((string) config('tenancy.platform_admin_host', 'admin.solentjo.com'));
        if ($this->normalizeHost($request->getHost()) !== $adminHost) {
            abort(404);
        }

        $user = Auth::user();
        if (!$user || !($user->is_admin ?? false)) {
            abort(403);
        }

        $allowedEmails = array_map('strtolower', (array) config('tenancy.platform_admin_emails', []));
        if (count($allowedEmails) === 0 || in_array(strtolower((string) $user->email), $allowedEmails, true)) {
            return $next($request);
        }

        abort(403);
    }

    private function normalizeHost(?string $host): string
    {
        return preg_replace('/^www\./', '', strtolower(trim((string) $host)));
    }
}
