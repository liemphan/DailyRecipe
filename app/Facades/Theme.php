<?php

namespace DailyRecipe\Facades;

use DailyRecipe\Theming\ThemeService;
use Illuminate\Support\Facades\Facade;

class Theme extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return ThemeService::class;
    }
}
