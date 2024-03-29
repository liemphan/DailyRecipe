<?php

namespace DailyRecipe\Uploads;

use DailyRecipe\Auth\Permissions\PermissionService;
use DailyRecipe\Entities\Models\Page;
use DailyRecipe\Entities\Models\Recipe;
use DailyRecipe\Exceptions\ImageUploadException;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageRepo
{
    protected $imageService;
    protected $restrictionService;

    /**
     * ImageRepo constructor.
     */
    public function __construct(ImageService $imageService, PermissionService $permissionService)
    {
        $this->imageService = $imageService;
        $this->restrictionService = $permissionService;
    }

    /**
     * Get an image with the given id.
     */
    public function getById($id): Image
    {
        return Image::query()->findOrFail($id);
    }

    /**
     * Execute a paginated query, returning in a standard format.
     * Also runs the query through the restriction system.
     */
    private function returnPaginated($query, $page = 1, $pageSize = 24): array
    {
        $images = $query->orderBy('created_at', 'desc')->skip($pageSize * ($page - 1))->take($pageSize + 1)->get();
        $hasMore = count($images) > $pageSize;

        $returnImages = $images->take($pageSize);
        $returnImages->each(function (Image $image) {
            $this->loadThumbs($image);
        });

        return [
            'images' => $returnImages,
            'has_more' => $hasMore,
        ];
    }

    /**
     * Fetch a list of images in a paginated format, filtered by image type.
     * Can be filtered by uploaded to and also by name.
     */
    public function getPaginatedByType(
        string   $type,
        int      $page = 0,
        int      $pageSize = 24,
        int      $uploadedTo = null,
        string   $search = null,
        callable $whereClause = null
    ): array
    {
        $imageQuery = Image::query()->where('type', '=', strtolower($type));

        if ($uploadedTo !== null) {
            $imageQuery = $imageQuery->where('uploaded_to', '=', $uploadedTo);
        }

        if ($search !== null) {
            $imageQuery = $imageQuery->where('name', 'LIKE', '%' . $search . '%');
        }

        // Filter by page access
        $imageQuery = $this->restrictionService->filterRelatedEntity(Recipe::class, $imageQuery, 'images', 'uploaded_to');

        if ($whereClause !== null) {
            $imageQuery = $imageQuery->where($whereClause);
        }

        return $this->returnPaginated($imageQuery, $page, $pageSize);
    }

    /**
     * Get paginated gallery images within a specific page or recipe.
     */
    public function getEntityFiltered(
        string $type,
        string $filterType = null,
        int    $page = 0,
        int    $pageSize = 24,
        int    $uploadedTo = null,
        string $search = null
    ): array
    {
        /** @var Recipe $contextPage */
        $contextPage = Recipe::visible()->findOrFail($uploadedTo);
        $parentFilter = null;

        if ($filterType === 'recipe' || $filterType === 'page') {
            $parentFilter = function (Builder $query) use ($filterType, $contextPage) {
                if ($filterType === 'page') {
                    $query->where('uploaded_to', '=', $contextPage->id);
                } elseif ($filterType === 'recipe') {
                    $validPageIds = $contextPage->pages()
                        ->scopes('visible')
                        ->pluck('id')
                        ->toArray();
                    $query->whereIn('uploaded_to', $validPageIds);
                }
            };
        }

        return $this->getPaginatedByType($type, $page, $pageSize, null, $search, $parentFilter);
    }

    /**
     * Save a new image into storage and return the new image.
     *
     * @throws ImageUploadException
     */
    public function saveNew(UploadedFile $uploadFile, string $type, int $uploadedTo = 0, int $resizeWidth = null, int $resizeHeight = null, bool $keepRatio = true): Image
    {
        $image = $this->imageService->saveNewFromUpload($uploadFile, $type, $uploadedTo, $resizeWidth, $resizeHeight, $keepRatio);
        $this->loadThumbs($image);

        return $image;
    }

    /**
     * Save a new image from an existing image data string.
     *
     * @throws ImageUploadException
     */
    public function saveNewFromData(string $imageName, string $imageData, string $type, int $uploadedTo = 0): Image
    {
        $image = $this->imageService->saveNew($imageName, $imageData, $type, $uploadedTo);
        $this->loadThumbs($image);

        return $image;
    }

    /**
     * Save a drawing in the database.
     *
     * @throws ImageUploadException
     */
    public function saveDrawing(string $base64Uri, int $uploadedTo): Image
    {
        $name = 'Drawing-' . user()->id . '-' . time() . '.png';

        return $this->imageService->saveNewFromBase64Uri($base64Uri, $name, 'drawio', $uploadedTo);
    }

    /**
     * Update the details of an image via an array of properties.
     *
     * @throws Exception
     */
    public function updateImageDetails(Image $image, $updateDetails): Image
    {
        $image->fill($updateDetails);
        $image->save();
        $this->loadThumbs($image);

        return $image;
    }

    /**
     * Destroys an Image object along with its revisions, files and thumbnails.
     *
     * @throws Exception
     */
    public function destroyImage(Image $image = null): void
    {
        if ($image) {
            $this->imageService->destroy($image);
        }
    }

    /**
     * Destroy all images of a certain type.
     *
     * @throws Exception
     */
    public function destroyByType(string $imageType): void
    {
        $images = Image::query()->where('type', '=', $imageType)->get();
        foreach ($images as $image) {
            $this->destroyImage($image);
        }
    }

    /**
     * Load thumbnails onto an image object.
     */
    public function loadThumbs(Image $image): void
    {
        $image->setAttribute('thumbs', [
            'gallery' => $this->getThumbnail($image, 150, 150, false),
            'display' => $this->getThumbnail($image, 1680, null, true),
        ]);
    }

    /**
     * Get the thumbnail for an image.
     * If $keepRatio is true only the width will be used.
     * Checks the cache then storage to avoid creating / accessing the filesystem on every check.
     */
    protected function getThumbnail(Image $image, ?int $width, ?int $height, bool $keepRatio): ?string
    {
        try {
            return $this->imageService->getThumbnail($image, $width, $height, $keepRatio);
        } catch (Exception $exception) {
            return null;
        }
    }

    /**
     * Get the raw image data from an Image.
     */
    public function getImageData(Image $image): ?string
    {
        try {
            return $this->imageService->getImageData($image);
        } catch (Exception $exception) {
            return null;
        }
    }

    /**
     * Get the user visible pages using the given image.
     */
    public function getPagesUsingImage(Image $image): array
    {
        $pages = Recipe::visible()
            ->where('html', 'like', '%' . $image->url . '%')
            ->get(['id', 'name', 'slug']);
//        , 'recipe_id'
        foreach ($pages as $page) {
            $page->setAttribute('url', $page->getUrl());
        }

        return $pages->all();
    }
}
