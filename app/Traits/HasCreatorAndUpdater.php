<?php

namespace DailyRecipe\Traits;

use DailyRecipe\Auth\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $created_by
 * @property int $updated_by
 * * @property int $user_id
 */
trait HasCreatorAndUpdater
{
    /**
     * Relation for the user that created this entity.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relation for the user that created this entity.
     */
    public function userId(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relation for the user that updated this entity.
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
