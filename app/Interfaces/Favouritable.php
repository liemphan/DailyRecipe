<?php

namespace DailyRecipe\Interfaces;

use Illuminate\Database\Eloquent\Relations\MorphMany;

interface Favouritable
{
    /**
     * Get the related favourite instances.
     */
    public function favourites(): MorphMany;
}
