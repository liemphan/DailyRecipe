<?php

namespace DailyRecipe\Entities\Tools;

use DailyRecipe\Entities\EntityProvider;
use DailyRecipe\Entities\Models\Recipe;
use DailyRecipe\Entities\Models\Recipemenu;
use DailyRecipe\Entities\Models\Chapter;
use DailyRecipe\Entities\Models\Deletion;
use DailyRecipe\Entities\Models\Entity;
use DailyRecipe\Entities\Models\HasCoverImage;
use DailyRecipe\Entities\Models\Page;
use DailyRecipe\Exceptions\NotifyException;
use DailyRecipe\Facades\Activity;
use DailyRecipe\Uploads\AttachmentService;
use DailyRecipe\Uploads\ImageService;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class TrashCan
{
    /**
     * Send a menu to the recycle bin.
     */
    public function softDestroyMenu(Recipemenu $menu)
    {
        Deletion::createForEntity($menu);
        $menu->delete();
    }

    /**
     * Send a recipe to the recycle bin.
     *
     * @throws Exception
     */
    public function softDestroyRecipe(Recipe $recipe)
    {
        Deletion::createForEntity($recipe);


        $recipe->delete();
    }

    /**
     * Send a chapter to the recycle bin.
     *
     * @throws Exception
     */
    public function softDestroyChapter(Chapter $chapter, bool $recordDelete = true)
    {
        if ($recordDelete) {
            Deletion::createForEntity($chapter);
        }

        if (count($chapter->pages) > 0) {
            foreach ($chapter->pages as $page) {
                $this->softDestroyPage($page, false);
            }
        }

        $chapter->delete();
    }

    /**
     * Send a page to the recycle bin.
     *
     * @throws Exception
     */
    public function softDestroyPage(Page $page, bool $recordDelete = true)
    {
        if ($recordDelete) {
            Deletion::createForEntity($page);
        }

        // Check if set as custom homepage & remove setting if not used or throw error if active
        $customHome = setting('app-homepage', '0:');
        if (intval($page->id) === intval(explode(':', $customHome)[0])) {
            if (setting('app-homepage-type') === 'page') {
                throw new NotifyException(trans('errors.page_custom_home_deletion'), $page->getUrl());
            }
            setting()->remove('app-homepage');
        }

        $page->delete();
    }

    /**
     * Remove a recipemenu from the system.
     *
     * @throws Exception
     */
    protected function destroyMenu(Recipemenu $menu): int
    {
        $this->destroyCommonRelations($menu);
        $menu->forceDelete();

        return 1;
    }

    /**
     * Remove a recipe from the system.
     * Destroys any child chapters and pages.
     *
     * @throws Exception
     */
    protected function destroyRecipe(Recipe $recipe): int
    {
        $count = 0;
        $pages = $recipe->pages()->withTrashed()->get();
        foreach ($pages as $page) {
            $this->destroyPage($page);
            $count++;
        }

        $chapters = $recipe->chapters()->withTrashed()->get();
        foreach ($chapters as $chapter) {
            $this->destroyChapter($chapter);
            $count++;
        }

        $this->destroyCommonRelations($recipe);
        $recipe->forceDelete();

        return $count + 1;
    }

    /**
     * Remove a chapter from the system.
     * Destroys all pages within.
     *
     * @throws Exception
     */
    protected function destroyChapter(Chapter $chapter): int
    {
        $count = 0;
        $pages = $chapter->pages()->withTrashed()->get();
        foreach ($pages as $page) {
            $this->destroyPage($page);
            $count++;
        }

        $this->destroyCommonRelations($chapter);
        $chapter->forceDelete();

        return $count + 1;
    }

    /**
     * Remove a page from the system.
     *
     * @throws Exception
     */
    protected function destroyPage(Page $page): int
    {
        $this->destroyCommonRelations($page);
        $page->allRevisions()->delete();

        // Delete Attached Files
        $attachmentService = app(AttachmentService::class);
        foreach ($page->attachments as $attachment) {
            $attachmentService->deleteFile($attachment);
        }

        $page->forceDelete();

        return 1;
    }

    /**
     * Get the total counts of those that have been trashed
     * but not yet fully deleted (In recycle bin).
     */
    public function getTrashedCounts(): array
    {
        $counts = [];

        foreach ((new EntityProvider())->all() as $key => $instance) {
            /** @var Builder<Entity> $query */
            $query = $instance->newQuery();
            $counts[$key] = $query->onlyTrashed()->count();
        }

        return $counts;
    }

    /**
     * Destroy all items that have pending deletions.
     *
     * @throws Exception
     */
    public function empty(): int
    {
        $deletions = Deletion::all();
        $deleteCount = 0;
        foreach ($deletions as $deletion) {
            $deleteCount += $this->destroyFromDeletion($deletion);
        }

        return $deleteCount;
    }

    /**
     * Destroy an element from the given deletion model.
     *
     * @throws Exception
     */
    public function destroyFromDeletion(Deletion $deletion): int
    {
        // We directly load the deletable element here just to ensure it still
        // exists in the event it has already been destroyed during this request.
        $entity = $deletion->deletable()->first();
        $count = 0;
        if ($entity) {
            $count = $this->destroyEntity($deletion->deletable);
        }
        $deletion->delete();

        return $count;
    }

    /**
     * Restore the content within the given deletion.
     *
     * @throws Exception
     */
    public function restoreFromDeletion(Deletion $deletion): int
    {
        $shouldRestore = true;
        $restoreCount = 0;

        if ($deletion->deletable instanceof Entity) {
            $parent = $deletion->deletable->getParent();
            if ($parent && $parent->trashed()) {
                $shouldRestore = false;
            }
        }

        if ($deletion->deletable instanceof Entity && $shouldRestore) {
            $restoreCount = $this->restoreEntity($deletion->deletable);
        }

        $deletion->delete();

        return $restoreCount;
    }

    /**
     * Automatically clear old content from the recycle bin
     * depending on the configured lifetime.
     * Returns the total number of deleted elements.
     *
     * @throws Exception
     */
    public function autoClearOld(): int
    {
        $lifetime = intval(config('app.recycle_bin_lifetime'));
        if ($lifetime < 0) {
            return 0;
        }

        $clearBeforeDate = Carbon::now()->addSeconds(10)->subDays($lifetime);
        $deleteCount = 0;

        $deletionsToRemove = Deletion::query()->where('created_at', '<', $clearBeforeDate)->get();
        foreach ($deletionsToRemove as $deletion) {
            $deleteCount += $this->destroyFromDeletion($deletion);
        }

        return $deleteCount;
    }

    /**
     * Restore an entity so it is essentially un-deleted.
     * Deletions on restored child elements will be removed during this restoration.
     */
    protected function restoreEntity(Entity $entity): int
    {
        $count = 1;
        $entity->restore();

        $restoreAction = function ($entity) use (&$count) {
            if ($entity->deletions_count > 0) {
                $entity->deletions()->delete();
            }

            $entity->restore();
            $count++;
        };

        if ($entity instanceof Chapter || $entity instanceof Recipe) {
            $entity->pages()->withTrashed()->withCount('deletions')->get()->each($restoreAction);
        }

        if ($entity instanceof Recipe) {
            $entity->chapters()->withTrashed()->withCount('deletions')->get()->each($restoreAction);
        }

        return $count;
    }

    /**
     * Destroy the given entity.
     *
     * @throws Exception
     */
    protected function destroyEntity(Entity $entity): int
    {
        if ($entity instanceof Page) {
            return $this->destroyPage($entity);
        }
        if ($entity instanceof Chapter) {
            return $this->destroyChapter($entity);
        }
        if ($entity instanceof Recipe) {
            return $this->destroyRecipe($entity);
        }
        if ($entity instanceof Recipemenu) {
            return $this->destroyMenu($entity);
        }

        return 0;
    }

    /**
     * Update entity relations to remove or update outstanding connections.
     */
    protected function destroyCommonRelations(Entity $entity)
    {
        Activity::removeEntity($entity);
        $entity->views()->delete();
        $entity->permissions()->delete();
        $entity->tags()->delete();
        $entity->comments()->delete();
        $entity->jointPermissions()->delete();
        $entity->searchTerms()->delete();
        $entity->deletions()->delete();
        $entity->favourites()->delete();

        if ($entity instanceof HasCoverImage && $entity->cover()->exists()) {
            $imageService = app()->make(ImageService::class);
            $imageService->destroy($entity->cover()->first());
        }
    }
}
