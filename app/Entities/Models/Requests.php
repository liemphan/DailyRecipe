<?php

namespace DailyRecipe\Entities\Models;

use Carbon\Carbon;
use DailyRecipe\Model;
use DailyRecipe\Traits\HasCreatorAndUpdater;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;


/**
 * Class RecipeRevision.
 *
 * @property int $id
 * @property int user_id
 * @property int status
 * @property int $created_by
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 */
class Requests extends Model
{
    use HasFactory;
    use HasCreatorAndUpdater;

    /**
     * Get the entity that this tag belongs to.
     */
    public function entity(): MorphTo
    {
        return $this->morphTo('entity');
    }

    /**
     * Get created date as a relative diff.
     *
     * @return mixed
     */
    public function getCreatedAttribute()
    {
        return $this->created_at->diffForHumans();
    }

}