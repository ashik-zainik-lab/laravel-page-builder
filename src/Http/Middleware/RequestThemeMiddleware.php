<?php

namespace Coderstm\PageBuilder\Http\Middleware;

use Closure;
use Coderstm\PageBuilder\Services\Theme;
use Illuminate\Http\Request;

class RequestThemeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->filled('theme')) {
            Theme::set($request->theme);
        }

        return $next($request);
    }
}
