<?php

namespace DailyRecipe\Http\Middleware;

use DailyRecipe\Facades\Theme;
use DailyRecipe\Theming\ThemeEvents;
use Closure;
use Illuminate\Http\Request;

class RunThemeActions
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $earlyResponse = Theme::dispatch(ThemeEvents::WEB_MIDDLEWARE_BEFORE, $request);
        if (!is_null($earlyResponse)) {
            return $earlyResponse;
        }

        $response = $next($request);
        $response = Theme::dispatch(ThemeEvents::WEB_MIDDLEWARE_AFTER, $request, $response) ?? $response;

        return $response;
    }
}
