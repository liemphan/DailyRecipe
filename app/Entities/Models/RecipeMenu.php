<?php

namespace DailyRecipe\Entities\Models;

use DailyRecipe\Uploads\Image;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class RecipeMenu extends Entity implements HasCoverImage
{
    use HasFactory;

    protected $table = 'recipemenus';

    public $searchFactor = 1.2;

    protected $fillable = ['name', 'description', 'image_id'];

    protected $hidden = ['restricted', 'image_id', 'deleted_at'];

    /**
     * Get the recipes in this menu.
     * Should not be used directly since does not take into account permissions.
     *
     * @return BelongsToMany
     */
    public function recipes()
    {
        return $this->belongsToMany(Recipe::class, 'recipemenus_recipes', 'recipemenu_id', 'recipe_id')
            ->withPivot('order')
            ->orderBy('order', 'asc');
    }

    /**
     * Related recipes that are visible to the current user.
     */
    public function visibleRecipes(): BelongsToMany
    {
        return $this->recipes()->scopes('visible');
    }

    /**
     * Get the url for this recipemenu.
     */
    public function getUrl(string $path = ''): string
    {
        return url('/menus/' . implode('/', [urlencode($this->slug), trim($path, '/')]));
    }

    /**
     * Returns RecipeMenu cover image, if cover does not exists return default cover image.
     *
     * @param int $width - Width of the image
     * @param int $height - Height of the image
     *
     * @return string
     */
    public function getRecipeCover($width = 440, $height = 250)
    {
        // TODO - Make generic, focused on recipes right now, Perhaps set-up a better image
        $default = 'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==';
        if (!$this->image_id) {
            return $default;
        }

        try {
            $cover = $this->cover ? url($this->cover->getThumb($width, $height, false)) : $default;
        } catch (Exception $err) {
            $cover = $default;
        }

        return $cover;
    }

    /**
     * Get the cover image of the menu.
     */
    public function cover(): BelongsTo
    {
        return $this->belongsTo(Image::class, 'image_id');
    }

    /**
     * Get the type of the image model that is used when storing a cover image.
     */
    public function coverImageTypeKey(): string
    {
        return 'cover_menu';
    }

    /**
     * Check if this menu contains the given recipe.
     *
     * @param Recipe $recipe
     *
     * @return bool
     */
    public function contains(Recipe $recipe): bool
    {
        return $this->recipes()->where('id', '=', $recipe->id)->count() > 0;
    }

    /**
     * Add a recipe to the end of this menu.
     *
     * @param Recipe $recipe
     */
    public function appendRecipe(Recipe $recipe)
    {
        if ($this->contains($recipe)) {
            return;
        }

        $maxOrder = $this->recipes()->max('order');
        $this->recipes()->attach($recipe->id, ['order' => $maxOrder + 1]);
    }
}
