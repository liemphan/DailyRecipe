<?php

namespace DailyRecipe\Providers;

use DailyRecipe\Api\ApiTokenGuard;
use DailyRecipe\Auth\Access\ExternalBaseUserProvider;
use DailyRecipe\Auth\Access\Guards\AsyncExternalBaseSessionGuard;
use DailyRecipe\Auth\Access\Guards\LdapSessionGuard;
use DailyRecipe\Auth\Access\LdapService;
use DailyRecipe\Auth\Access\LoginService;
use DailyRecipe\Auth\Access\RegistrationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Auth::extend('api-token', function ($app, $name, array $config) {
            return new ApiTokenGuard($app['request'], $app->make(LoginService::class));
        });

        Auth::extend('ldap-session', function ($app, $name, array $config) {
            $provider = Auth::createUserProvider($config['provider']);

            return new LdapSessionGuard(
                $name,
                $provider,
                $app['session.store'],
                $app[LdapService::class],
                $app[RegistrationService::class]
            );
        });

        Auth::extend('async-external-session', function ($app, $name, array $config) {
            $provider = Auth::createUserProvider($config['provider']);

            return new AsyncExternalBaseSessionGuard(
                $name,
                $provider,
                $app['session.store'],
                $app[RegistrationService::class]
            );
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        Auth::provider('external-users', function ($app, array $config) {
            return new ExternalBaseUserProvider($config['model']);
        });
    }
}
