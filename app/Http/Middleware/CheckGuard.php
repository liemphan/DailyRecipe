<?php

namespace DailyRecipe\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckGuard
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string                   $allowedGuards
     *
     * @return mixed
     */
    public function handle($request, Closure $next, ...$allowedGuards)
    {
        $activeGuard = config('auth.method');
        if (!in_array($activeGuard, $allowedGuards)) {
            session()->flash('error', trans('errors.permission'));

            return redirect('/');
        }

        return $next($request);
    }
}
