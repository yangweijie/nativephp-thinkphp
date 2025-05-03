<?php

namespace native\thinkphp\http\middleware;

use Closure;
use think\facade\Request;


class PreventRegularBrowserAccess
{
    public function handle(Request $request, Closure $next)
    {
        // Explicitly skip for the cookie-setting route
        if ($request->url() === '_native/api/cookie') {
            return $next($request);
        }

        $cookie = $request->cookie('_php_native');
        $header = $request->header('X-NativePHP-Secret');

        if ($cookie && $cookie === config('nativephp-internal.secret')) {
            return $next($request);
        }

        if ($header && $header === config('nativephp-internal.secret')) {
            return $next($request);
        }

        return abort(403);
    }
}
