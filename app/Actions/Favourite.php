<?php

namespace DailyRecipe\Actions;

use DailyRecipe\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Favourite extends Model
{
    protected $fillable = ['user_id'];

    /**
     * Get the related model that can be favourited.
     */
    public function favouritable(): MorphTo
    {
        return $this->morphTo();
    }
}
