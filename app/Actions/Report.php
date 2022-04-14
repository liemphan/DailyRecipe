<?php

namespace DailyRecipe\Actions;

use DailyRecipe\Auth\User;
use DailyRecipe\Model;
use DailyRecipe\Traits\HasCreatorAndUpdater;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property User $user
 * @property int $id
 * @property string $content
 * @property string $description
 * @property int $status
 */
class Report extends Model
{
    use HasFactory;
    use HasCreatorAndUpdater;

    protected $fillable = ['content', 'description', 'status' , 'user_id'];
    protected $hidden = ['id', 'entity_id', 'entity_type', 'created_at', 'updated_at'];

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
    /**
     * Get the user this activity relates to.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
