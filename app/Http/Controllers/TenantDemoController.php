<?php

namespace App\Http\Controllers;

use App\Support\Tenancy\TenantDemoDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TenantDemoController extends Controller
{
    public function update(Request $request, TenantDemoDatabase $service)
    {
        abort_unless($request->user() && $request->user()->is_admin, 403);
        $real = app()->bound('app.tenant_real_context') ? app('app.tenant_real_context') : null;
        abort_unless($service->supports($request, $real), 404);
        $request->validate(['enabled' => ['required', 'boolean']]);
        $enabled = $request->boolean('enabled');
        if ($enabled) {
            try {
                $service->verify($real);
            } catch (\Throwable $exception) {
                report($exception);
                return redirect()->route('home')->with('error', 'Sample data is not ready for this website yet. Your real data has not changed.');
            }
        }
        $key = $service->sessionKey($real, $request->user());
        $request->session()->put($key, [
            'active' => $enabled,
            'switched' => true,
            'preference_version' => TenantDemoDatabase::PREFERENCE_VERSION,
            'token' => Str::random(32),
        ]);
        $request->session()->forget('dashboard.sample_data');
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
