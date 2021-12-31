<?php

namespace DailyRecipe\Entities\Models;

use DailyRecipe\Uploads\Attachment;
use DailyRecipe\Uploads\Image;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

/**
 * Class Recipe.
 *
 * @property string                                   $description
 * @property int                                      $image_id
 * @property Image|null                               $cover
 * @property \Illuminate\Database\Eloquent\Collection $chapters
 * @property \Illuminate\Database\Eloquent\Collection $pages
 * @property \Illuminate\Database\Eloquent\Collection $directPages
 * @property string     $html
 * @property string     $markdown
 * @property string     $text
 * @property bool       $template
 * @property bool       $draft
 * @property int        $revision_count
 */
class Recipe extends Entity implements HasCoverImage
{
    use HasFactory;

    public $searchFactor = 1.2;

    protected $fillable = ['name', 'description','priority'];
    protected $hidden = ['restricted', 'pivot', 'image_id', 'deleted_at','html', 'markdown', 'text',];

    public $textField = 'text';

    protected $casts = [
        'draft'    => 'boolean',
        'template' => 'boolean',
    ];
    /**
     * Get the url for this recipe.
     */
    public function getUrl(string $path = ''): string
    {

        return url('/recipes/' . implode('/', [urlencode($this->slug), trim($path, '/')]));
    }
    /**
     * Get the url of this page.
     */
    public function getUrlContent(string $path = ''): string
    {
        $parts = [
            'recipes',
            urlencode($this->slug),
            $this->draft ? 'draft' : 'page',
            $this->draft ? $this->id : urlencode($this->slug),
            trim($path, '/'),
        ];

        return url('/' . implode('/', $parts));
    }

    /**
     * Returns recipe cover image, if recipe cover not exists return default cover image.
     *
     * @param int $width  - Width of the image
     * @param int $height - Height of the image
     *
     * @return string
     */
    public function getRecipeCover($width = 440, $height = 250)
    {
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
     * Get the cover image of the recipe.
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
        return 'cover_recipe';
    }

    /**
     * Get all pages within this recipe.
     */
    public function pages(): HasMany
    {
        return $this->hasMany(Page::class);
    }

    /**
     * Get the direct child pages of this recipe.
     */
    public function directPages(): HasMany
    {
        return $this->pages()->where('chapter_id', '=', '0');
    }

    /**
     * Get all chapters within this recipe.
     */
    public function chapters(): HasMany
    {
        return $this->hasMany(Chapter::class);
    }

    /**
     * Get the menus this recipe is contained within.
     */
    public function menus(): BelongsToMany
    {
        return $this->belongsToMany(Recipemenu::class, 'recipemenus_recipes', 'recipe_id', 'recipemenu_id');
    }

    /**
     * Get the direct child items within this recipe.
     */
    public function getDirectChildren(): Collection
    {
        $pages = $this->directPages()->scopes('visible')->get();
        $chapters = $this->chapters()->scopes('visible')->get();

        return $pages->concat($chapters)->sortBy('priority')->sortByDesc('draft');
    }

    /**
     * Get the attachments assigned to this page.
     *
     * @return HasMany
     */
    public function attachments()
    {
        return $this->hasMany(Attachment::class, 'uploaded_to')->orderBy('order', 'asc');
    }
}
