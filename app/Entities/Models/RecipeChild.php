<?php

namespace DailyRecipe\Entities\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class RecipeChild.
 *
 * @property int $recipe_id
 * @property string $recipe_slug
 * @property Recipe $recipe
 *
 * @method Builder whereSlugs(string $recipeSlug, string $childSlug)
 */
abstract class RecipeChild extends Entity
{
    protected static function boot()
    {
        parent::boot();

        // Load recipe slugs onto these models by default during query-time
        static::addGlobalScope('recipe_slug', function (Builder $builder) {
            $builder->addSelect(['recipe_slug' => function ($builder) {
                $builder->select('slug')
                    ->from('recipes')
                    ->whereColumn('recipes.id', '=', 'recipe_id');
            }]);
        });
    }

    /**
     * Scope a query to find items where the child has the given childSlug
     * where its parent has the recipeSlug.
     */
    public function scopeWhereSlugs(Builder $query, string $recipeSlug, string $childSlug)
    {
        return $query->with('recipe')
            ->whereHas('recipe', function (Builder $query) use ($recipeSlug) {
                $query->where('slug', '=', $recipeSlug);
            })
            ->where('slug', '=', $childSlug);
    }

    /**
     * Get the recipe this page sits in.
     */
    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class)->withTrashed();
    }

    /**
     * Change the recipe that this entity belongs to.
     */
    public function changeRecipe(int $newRecipeId): Entity
    {
        $this->recipe_id = $newRecipeId;
        $this->refreshSlug();
        $this->save();
        $this->refresh();

        foreach ($this->pages()->withTrashed()->get() as $page) {
            $page->changeRecipe($newRecipeId);
        }


        return $this;
    }

    /**
     * Get the pages that this chapter contains.
     *
     * @return HasMany<Recipe>
     */
    public function pages(string $dir = 'ASC'): HasMany
    {
        return $this->hasMany(Recipe::class)->orderBy('priority', $dir);
    }
}
