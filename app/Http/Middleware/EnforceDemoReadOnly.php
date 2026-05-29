<?php

namespace App\Http\Middleware;

use App\Support\DemoMode;
use Closure;
use Illuminate\Http\Request;

class EnforceDemoReadOnly
{
    public function handle(Request $request, Closure $next)
    {
        if (! DemoMode::isDemoRequest($request)) {
            return $next($request);
        }

        if ($this->isAllowedWrite($request) || $request->isMethodSafe()) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Demo mode is read-only. Changes are not saved on demo.ceralis.com.',
            ], 423);
        }

        return back()->with('error', 'Demo mode is read-only. Changes are not saved on demo.ceralis.com.');
    }

    private function isAllowedWrite(Request $request): bool
    {
        return in_array($request->route()?->getName(), ['login', 'logout'], true);
    }
}
