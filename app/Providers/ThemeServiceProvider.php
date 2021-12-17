<?php

namespace DailyRecipe\Providers;

use DailyRecipe\Theming\ThemeEvents;
use DailyRecipe\Theming\ThemeService;
use Illuminate\Support\ServiceProvider;

class ThemeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(ThemeService::class, function ($app) {
            return new ThemeService();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $themeService = $this->app->make(ThemeService::class);
        $themeService->readThemeActions();
        $themeService->dispatch(ThemeEvents::APP_BOOT, $this->app);
    }
}
