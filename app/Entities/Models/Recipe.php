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
 * @property string $description
 * @property int $image_id
 * @property Image|null $cover
 * @property int $priority
 * @property string $html
 * @property string $markdown
 * @property string $text
 * @property bool $template
 * @property bool $draft
 * @property int $revision_count
 */
class Recipe extends Entity implements HasCoverImage
{
    use HasFactory;

    public static $listAttributes = ['name', 'id', 'slug', 'draft', 'template','html', 'text', 'created_at', 'updated_at', 'priority'];
    public static $contentAttributes = ['name', 'id', 'slug', 'draft', 'template', 'html', 'text', 'created_at', 'updated_at', 'priority'];

    public $searchFactor = 1.2;

    protected $fillable = ['name', 'description', 'priority'];
    protected $hidden = ['restricted', 'pivot', 'image_id', 'deleted_at', 'html', 'markdown', 'text',];

    public $textField = 'text';

    protected $casts = [
        'draft' => 'boolean',
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
            $this->draft ? 'draft' : 'content',
            trim($path, '/'),
        ];

        return url('/' . implode('/', $parts));
    }

    /**
     * Returns recipe cover image, if recipe cover not exists return default cover image.
     *
     * @param int $width - Width of the image
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
     * Get the menus this recipe is contained within.
     */
    public function menus(): BelongsToMany
    {
        return $this->belongsToMany(Recipemenu::class, 'recipemenus_recipes', 'recipe_id', 'recipemenu_id');
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

    /**
     * Get the associated page revisions, ordered by created date.
     * Only provides actual saved page revision instances, Not drafts.
     */
    public function revisions(): HasMany
    {
        return $this->allRevisions()
            ->where('type', '=', 'version')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc');
    }
    /**
     * Get all revision instances assigned to this page.
     * Includes all types of revisions.
     */
    public function allRevisions(): HasMany
    {
        return $this->hasMany(RecipeRevision::class);
    }

    /**
     * Get the current revision for the page if existing.
     *
     * @return PageRevision|null
     */
    public function getCurrentRevision()
    {
        return $this->revisions()->first();
    }
}
