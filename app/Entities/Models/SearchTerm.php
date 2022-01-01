<?php

namespace DailyRecipe\Entities\Models;

use DailyRecipe\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SearchTerm extends Model
{
    protected $fillable = ['term', 'entity_id', 'entity_type', 'score'];
    public $timestamps = false;

    /**
     * Get the entity that this term belongs to.
     *
     * @return MorphTo
     */
    public function entity()
    {
        return $this->morphTo('entity');
    }
}
