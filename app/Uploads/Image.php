<?php

namespace DailyRecipe\Uploads;

use DailyRecipe\Entities\Models\Page;
use DailyRecipe\Model;
use DailyRecipe\Traits\HasCreatorAndUpdater;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int    $id
 * @property string $name
 * @property string $url
 * @property string $path
 * @property string $type
 * @property int    $uploaded_to
 * @property int    $created_by
 * @property int    $updated_by
 */
class Image extends Model
{
    use HasFactory;
    use HasCreatorAndUpdater;

    protected $fillable = ['name'];
    protected $hidden = [];

    /**
     * Get a thumbnail for this image.
     *
     * @throws Exception
     */
    public function getThumb(int $width, int $height, bool $keepRatio = false): string
    {
        return app()->make(ImageService::class)->getThumbnail($this, $width, $height, $keepRatio);
    }

    /**
     * Get the page this image has been uploaded to.
     * Only applicable to gallery or drawio image types.
     */
    public function getPage(): ?Page
    {
        return $this->belongsTo(Page::class, 'uploaded_to')->first();
    }
}
