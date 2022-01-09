<?php

namespace DailyRecipe\Entities\Models;

use DailyRecipe\Auth\User;
use DailyRecipe\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class RecipeRevision.
 *
 * @property int $recipe_id
 * @property string $slug
 * @property int $created_by
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $type
 * @property string $summary
 * @property string $markdown
 * @property string $html
 * @property int $revision_number
 * @property Recipe $recipe
 * @property-read ?User $createdBy
 */
class RecipeRevision extends Model
{
    protected $fillable = ['name', 'html', 'text', 'markdown', 'summary'];

    /**
     * Get the user that created the page revision.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the page this revision originates from.
     */
    public function page(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    /**
     * Get the url for this revision.
     *
     * @param null|string $path
     *
     * @return string
     */
    public function getUrl($path = null)
    {
        $url = $this->recipe->getUrl() . '/revisions/' . $this->id;
        if ($path) {
            return $url . '/' . trim($path, '/');
        }

        return $url;
    }

    /**
     * Get the previous revision for the same page if existing.
     */
    public function getPrevious(): ?RecipeRevision
    {
        $id = static::newQuery()->where('recipe_id', '=', $this->recipe_id)
            ->where('id', '<', $this->id)
            ->max('id');

        if ($id) {
            return static::query()->find($id);
        }

        return null;
    }

    /**
     * Allows checking of the exact class, Used to check entity type.
     * Included here to align with entities in similar use cases.
     * (Yup, Bit of an awkward hack).
     *
     * @deprecated Use instanceof instead.
     */
    public static function isA(string $type): bool
    {
        return $type === 'revision';
    }
}