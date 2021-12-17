<?php

namespace DailyRecipe\Http\Controllers\Auth;

use DailyRecipe\Auth\Access\LoginService;
use DailyRecipe\Auth\User;
use DailyRecipe\Exceptions\NotFoundException;

trait HandlesPartialLogins
{
    /**
     * @throws NotFoundException
     */
    protected function currentOrLastAttemptedUser(): User
    {
        $loginService = app()->make(LoginService::class);
        $user = auth()->user() ?? $loginService->getLastLoginAttemptUser();

        if (!$user) {
            throw new NotFoundException('A user for this action could not be found');
        }

        return $user;
    }
}
