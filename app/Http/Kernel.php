<?php

namespace DailyRecipe\Http;

use DailyRecipe\Http\Middleware\ApiAuthenticate;
use DailyRecipe\Http\Middleware\ApplyCspRules;
use DailyRecipe\Http\Middleware\Authenticate;
use DailyRecipe\Http\Middleware\AuthenticatedOrPendingMfa;
use DailyRecipe\Http\Middleware\CheckEmailConfirmed;
use DailyRecipe\Http\Middleware\CheckGuard;
use DailyRecipe\Http\Middleware\CheckUserHasPermission;
use DailyRecipe\Http\Middleware\EncryptCookies;
use DailyRecipe\Http\Middleware\Localization;
use DailyRecipe\Http\Middleware\PreventAuthenticatedResponseCaching;
use DailyRecipe\Http\Middleware\PreventRequestsDuringMaintenance;
use DailyRecipe\Http\Middleware\RedirectIfAuthenticated;
use DailyRecipe\Http\Middleware\RunThemeActions;
use DailyRecipe\Http\Middleware\StartSessionIfCookieExists;
use DailyRecipe\Http\Middleware\ThrottleApiRequests;
use DailyRecipe\Http\Middleware\TrimStrings;
use DailyRecipe\Http\Middleware\TrustProxies;
use DailyRecipe\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Foundation\Http\Middleware\ValidatePostSize;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     * These middleware are run during every request to your application.
     */
    protected $middleware = [
        PreventRequestsDuringMaintenance::class,
        ValidatePostSize::class,
        TrimStrings::class,
        TrustProxies::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            ApplyCspRules::class,
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
            PreventAuthenticatedResponseCaching::class,
            CheckEmailConfirmed::class,
            RunThemeActions::class,
            Localization::class,
        ],
        'api' => [
            ThrottleApiRequests::class,
            EncryptCookies::class,
            StartSessionIfCookieExists::class,
            ApiAuthenticate::class,
            PreventAuthenticatedResponseCaching::class,
            CheckEmailConfirmed::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => Authenticate::class,
        'can' => CheckUserHasPermission::class,
        'guest' => RedirectIfAuthenticated::class,
        'throttle' => ThrottleRequests::class,
        'guard' => CheckGuard::class,
        'mfa-setup' => AuthenticatedOrPendingMfa::class,
    ];
}
