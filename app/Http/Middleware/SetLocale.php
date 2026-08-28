<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $supported = config('localization.supported', ['ar', 'en']);
        $cookieName = config('localization.cookie', 'ui_locale');
        $queryName = config('localization.query_parameter', 'ui_locale');

        if ($request->is('api/*')) {
            App::setLocale('en');

            return $next($request);
        }

        $requestedLocale = $request->query($queryName);
        if (is_string($requestedLocale) && in_array($requestedLocale, $supported, true)) {
            App::setLocale($requestedLocale);

            $query = $request->query();
            unset($query[$queryName]);
            $target = $request->url();
            if ($query !== []) {
                $target .= '?' . http_build_query($query);
            }

            return redirect()->to($target)->withCookie(cookie(
                $cookieName,
                $requestedLocale,
                60 * 24 * 365,
                '/',
                null,
                $request->isSecure(),
                true,
                false,
                'lax'
            ));
        }

        $locale = $request->cookie($cookieName, config('localization.default', 'ar'));
        if (!in_array($locale, $supported, true)) {
            $locale = config('localization.default', 'ar');
        }

        App::setLocale($locale);

        return $next($request);
    }
}
