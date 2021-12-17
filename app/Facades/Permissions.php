<?php

namespace DailyRecipe\Facades;

use Illuminate\Support\Facades\Facade;

class Permissions extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'permissions';
    }
}
