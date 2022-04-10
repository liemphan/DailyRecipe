<?php

namespace DailyRecipe\Actions;

use DailyRecipe\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use DailyRecipe\Traits\HasCreatorAndUpdater;
/**
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
    protected $hidden = ['id', 'entity_id', 'entity_type', 'created_at'];

    /**
     * Get the entity that this tag belongs to.
     */
    public function entity(): MorphTo
    {
        return $this->morphTo('entity');
    }

    /**
     * Get the url for this recipe.
     */
    public function getUrl(string $path = ''): string
    {

        return url('/recipes/' . implode('/', [urlencode($this->slug), trim($path, '/')]));
    }

    /**
     * Get a full URL to start a tag name search for this tag name.
     */
    public function nameUrl(): string
    {
        return url('/search?term=%5B' . urlencode($this->name) . '%5D');
    }

    /**
     * Get a full URL to start a tag name and value search for this tag's values.
     */
    public function valueUrl(): string
    {
        return url('/search?term=%5B' . urlencode($this->name) . '%3D' . urlencode($this->value) . '%5D');
    }
}
