<?php

namespace DailyRecipe\Entities\Repos;

use DailyRecipe\Actions\TagRepo;
use DailyRecipe\Entities\Models\Entity;
use DailyRecipe\Entities\Models\HasCoverImage;
use DailyRecipe\Exceptions\ImageUploadException;
use DailyRecipe\Uploads\ImageRepo;
use Exception;
use Illuminate\Http\UploadedFile;

class BaseRepo
{
    protected $tagRepo;
    protected $imageRepo;

    public function __construct(TagRepo $tagRepo, ImageRepo $imageRepo)
    {
        $this->tagRepo = $tagRepo;
        $this->imageRepo = $imageRepo;
    }

    /**
     * Create a new entity in the system.
     */
    public function create(Entity $entity, array $input)
    {
        $entity->fill($input);
        $entity->forceFill([
            'created_by' => user()->id,
            'updated_by' => user()->id,
            'owned_by' => user()->id,
            'draft' => true,
        ]);

        $entity->refreshSlug();
        $entity->save();

        if (isset($input['tags'])) {
            $this->tagRepo->saveTagsToEntity($entity, $input['tags']);
        }

        $entity->rebuildPermissions();
        $entity->indexForSearch();

    }

    /**
     * Create a new entity in the system.
     */
    public function createMenus(Entity $entity, array $input)
    {
        $entity->fill($input);
        $entity->forceFill([
            'created_by' => user()->id,
            'updated_by' => user()->id,
            'owned_by' => user()->id,
        ]);

        $entity->refreshSlug();
        $entity->save();

        if (isset($input['tags'])) {
            $this->tagRepo->saveTagsToEntity($entity, $input['tags']);
        }

        $entity->rebuildPermissions();
        $entity->indexForSearch();

    }

    /**
     * Update the given entity.
     */
    public function update(Entity $entity, array $input)
    {
        $entity->fill($input);
        $entity->updated_by = user()->id;

        if ($entity->isDirty('name')) {
            $entity->refreshSlug();
        }

        $entity->save();

        if (isset($input['tags'])) {
            $this->tagRepo->saveTagsToEntity($entity, $input['tags']);
        }

        $entity->rebuildPermissions();
        $entity->indexForSearch();
    }

    /**
     * Update the given items' cover image, or clear it.
     *
     * @param Entity&HasCoverImage $entity
     *
     * @throws ImageUploadException
     * @throws Exception
     */
    public function updateCoverImage($entity, ?UploadedFile $coverImage, bool $removeImage = false)
    {
        if ($coverImage) {
            $this->imageRepo->destroyImage($entity->cover);
            $image = $this->imageRepo->saveNew($coverImage, 'cover_recipe', $entity->id, 512, 512, true);
            $entity->cover()->associate($image);
            $entity->save();
        }

        if ($removeImage) {
            $this->imageRepo->destroyImage($entity->cover);
            $entity->image_id = 0;
            $entity->save();
        }
    }
}
