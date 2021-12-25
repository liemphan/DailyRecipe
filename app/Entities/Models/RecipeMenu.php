<?php

namespace DailyRecipe\Entities\Models;

use DailyRecipe\Uploads\Image;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Recipemenu extends Entity implements HasCoverImage
{
    use HasFactory;

    protected $table = 'recipemenus';

    public $searchFactor = 1.2;

    protected $fillable = ['name', 'description', 'image_id'];

    protected $hidden = ['restricted', 'image_id', 'deleted_at'];

    /**
     * Get the books in this menu.
     * Should not be used directly since does not take into account permissions.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function books()
    {
        return $this->belongsToMany(Recipe::class, 'recipemenus_books', 'recipemenu_id', 'book_id')
            ->withPivot('order')
            ->orderBy('order', 'asc');
    }

    /**
     * Related books that are visible to the current user.
     */
    public function visibleRecipes(): BelongsToMany
    {
        return $this->books()->scopes('visible');
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
     * @param int $width  - Width of the image
     * @param int $height - Height of the image
     *
     * @return string
     */
    public function getBookCover($width = 440, $height = 250)
    {
        // TODO - Make generic, focused on books right now, Perhaps set-up a better image
        $default = 'data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==';
        if (!$this->image_id) {
            return $default;
        }

        try {
            $cover = $this->cover ? url($this->cover->getThumb($width, $height, false)) : $default;
        } catch (\Exception $err) {
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
     * Check if this menu contains the given book.
     *
     * @param Recipe $book
     *
     * @return bool
     */
    public function contains(Recipe $book): bool
    {
        return $this->books()->where('id', '=', $book->id)->count() > 0;
    }

    /**
     * Add a book to the end of this menu.
     *
     * @param Recipe $book
     */
    public function appendBook(Recipe $book)
    {
        if ($this->contains($book)) {
            return;
        }

        $maxOrder = $this->books()->max('order');
        $this->books()->attach($book->id, ['order' => $maxOrder + 1]);
    }
}
