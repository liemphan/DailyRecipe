<?php

namespace DailyRecipe\Auth\Permissions;

use DailyRecipe\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class EntityPermission extends Model
{
    protected $fillable = ['role_id', 'action'];
    public $timestamps = false;

    /**
     * Get all this restriction's attached entity.
     *
     * @return MorphTo
     */
    public function restrictable()
    {
        return $this->morphTo('restrictable');
    }
}
