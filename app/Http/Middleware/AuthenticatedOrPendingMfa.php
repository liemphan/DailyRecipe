<?php

namespace DailyRecipe\Http\Middleware;

use DailyRecipe\Auth\Access\LoginService;
use DailyRecipe\Auth\Access\Mfa\MfaSession;
use Closure;
use Illuminate\Http\Request;

class AuthenticatedOrPendingMfa
{
    protected $loginService;
    protected $mfaSession;

    public function __construct(LoginService $loginService, MfaSession $mfaSession)
    {
        $this->loginService = $loginService;
        $this->mfaSession = $mfaSession;
    }

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
        $user = auth()->user();
        $loggedIn = $user !== null;
        $lastAttemptUser = $this->loginService->getLastLoginAttemptUser();

        if ($loggedIn || ($lastAttemptUser && $this->mfaSession->isPendingMfaSetup($lastAttemptUser))) {
            return $next($request);
        }

        return redirect()->to(url('/login'));
    }
}
