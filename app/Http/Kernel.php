<?php

namespace DailyRecipe\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     * These middleware are run during every request to your application.
     */
    protected $middleware = [
        \DailyRecipe\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \DailyRecipe\Http\Middleware\TrimStrings::class,
        \DailyRecipe\Http\Middleware\TrustProxies::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \DailyRecipe\Http\Middleware\ApplyCspRules::class,
            \DailyRecipe\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \DailyRecipe\Http\Middleware\VerifyCsrfToken::class,
            \DailyRecipe\Http\Middleware\PreventAuthenticatedResponseCaching::class,
            \DailyRecipe\Http\Middleware\CheckEmailConfirmed::class,
            \DailyRecipe\Http\Middleware\RunThemeActions::class,
            \DailyRecipe\Http\Middleware\Localization::class,
        ],
        'api' => [
            \DailyRecipe\Http\Middleware\ThrottleApiRequests::class,
            \DailyRecipe\Http\Middleware\EncryptCookies::class,
            \DailyRecipe\Http\Middleware\StartSessionIfCookieExists::class,
            \DailyRecipe\Http\Middleware\ApiAuthenticate::class,
            \DailyRecipe\Http\Middleware\PreventAuthenticatedResponseCaching::class,
            \DailyRecipe\Http\Middleware\CheckEmailConfirmed::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth'       => \DailyRecipe\Http\Middleware\Authenticate::class,
        'can'        => \DailyRecipe\Http\Middleware\CheckUserHasPermission::class,
        'guest'      => \DailyRecipe\Http\Middleware\RedirectIfAuthenticated::class,
        'throttle'   => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'guard'      => \DailyRecipe\Http\Middleware\CheckGuard::class,
        'mfa-setup'  => \DailyRecipe\Http\Middleware\AuthenticatedOrPendingMfa::class,
    ];
}
