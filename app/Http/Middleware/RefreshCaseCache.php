<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\CaseCache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Log;

class RefreshCaseCache
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Only run for CaseController routes
//        if (str_contains(haystack: $request->route()->getActionName(), needle: 'CaseController')) {
//
//
//            $permissions = Cache::get('user' . Auth::user()->id);
//            $isAdmin = Auth()->user()->is_admin == 1 || ($permissions && $permissions->contains('permission_id', 122));
//
//            $cacheKey = 'dashboard_data_' . Auth()->user()->id . '_' . ($isAdmin ? 'admin' : 'user');
//                Log::info('Refreshed dashboard cache from middleware'.$cacheKey);
//                CaseCache::refresh($cacheKey);
//
//        }

        return $response;
    }
}
