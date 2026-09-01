<?php

namespace App\Http\Middleware;

use App\Support\Tenancy\TenantDemoDatabase;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class UseTenantDemoDatabase
{
    public function handle(Request $request, Closure $next)
    {
        $service = app(TenantDemoDatabase::class);
        $real = app()->bound('app.tenant_context') ? app('app.tenant_context') : null;
        app()->instance('app.tenant_demo_active', false);
        View::share('tenantDemoActive', false);
        View::share('tenantDemoSupported', false);

        // Resolve identity and branding on the real connection BEFORE changing business data.
        $user = Auth::guard('web')->user();
        if (! $user || ! $service->supports($request, $real)) {
            return $next($request);
        }

        $originalConnection = DB::getDefaultConnection();
        foreach (['web', 'clients'] as $guard) {
            if ($identity = Auth::guard($guard)->user()) {
                $identity->setConnection($originalConnection);
            }
        }
        app()->instance('app.tenant_real_context', $real);
        $key = $service->sessionKey($real, $user);
        $request->attributes->set('tenant_demo_session_key', $key);
        if (! $request->session()->has($key . '.token')) {
            $request->session()->put($key . '.token', Str::random(32));
        }
        View::share('tenantDemoSupported', (bool) $user->is_admin);
        View::share('tenantDataContextToken', $request->session()->get($key . '.token'));

        $savedPreferenceVersion = (int) $request->session()->get($key . '.preference_version', 0);
        // A new default applies once to older sessions; later explicit choices remain respected.
        $active = $savedPreferenceVersion === TenantDemoDatabase::PREFERENCE_VERSION
            ? (bool) $request->session()->get($key . '.active', (bool) $user->is_admin)
            : (bool) $user->is_admin;
        // The switch and logout must remain usable even if the sample database is unavailable.
        $isControl = in_array($request->route()?->getName(), ['tenant-demo.update', 'logout'], true);
        try {
            if ($active && ! $isControl) {
                abort_unless($user->is_admin, 403, 'Only administrators can preview sample data.');
                try {
                    $service->activate($real);
                } catch (\Throwable $exception) {
                    report($exception);
                    return response()->view('errors.tenant-demo-unavailable', [], 503);
                }
                View::share('tenantDemoActive', true);
            }
            $response = $next($request);
            if ($active || $request->session()->get($key . '.switched')) {
                $response->headers->set('Cache-Control', 'private, no-store, max-age=0');
            }
            return $response;
        } finally {
            DB::setDefaultConnection($originalConnection);
            app()->instance('app.tenant_context', $real);
        }
    }
}
