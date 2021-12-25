<?php

namespace DailyRecipe\Entities\Repos;

use DailyRecipe\Actions\ActivityType;
use DailyRecipe\Entities\Models\Recipe;
use DailyRecipe\Entities\Models\Chapter;
use DailyRecipe\Entities\Tools\RecipeContents;
use DailyRecipe\Entities\Tools\TrashCan;
use DailyRecipe\Exceptions\MoveOperationException;
use DailyRecipe\Exceptions\NotFoundException;
use DailyRecipe\Facades\Activity;
use Exception;

class ChapterRepo
{
    protected $baseRepo;

    /**
     * ChapterRepo constructor.
     */
    public function __construct(BaseRepo $baseRepo)
    {
        $this->baseRepo = $baseRepo;
    }

    /**
     * Get a chapter via the slug.
     *
     * @throws NotFoundException
     */
    public function getBySlug(string $bookSlug, string $chapterSlug): Chapter
    {
        $chapter = Chapter::visible()->whereSlugs($bookSlug, $chapterSlug)->first();

        if ($chapter === null) {
            throw new NotFoundException(trans('errors.chapter_not_found'));
        }

        return $chapter;
    }

    /**
     * Create a new chapter in the system.
     */
    public function create(array $input, Recipe $parentBook): Chapter
    {
        $chapter = new Chapter();
        $chapter->recipe_id = $parentBook->id;
        $chapter->priority = (new RecipeContents($parentBook))->getLastPriority() + 1;
        $this->baseRepo->create($chapter, $input);
        Activity::addForEntity($chapter, ActivityType::CHAPTER_CREATE);

        return $chapter;
    }

    /**
     * Update the given chapter.
     */
    public function update(Chapter $chapter, array $input): Chapter
    {
        $this->baseRepo->update($chapter, $input);
        Activity::addForEntity($chapter, ActivityType::CHAPTER_UPDATE);

        return $chapter;
    }

    /**
     * Remove a chapter from the system.
     *
     * @throws Exception
     */
    public function destroy(Chapter $chapter)
    {
        $trashCan = new TrashCan();
        $trashCan->softDestroyChapter($chapter);
        Activity::addForEntity($chapter, ActivityType::CHAPTER_DELETE);
        $trashCan->autoClearOld();
    }

    /**
     * Move the given chapter into a new parent book.
     * The $parentIdentifier must be a string of the following format:
     * 'book:<id>' (book:5).
     *
     * @throws MoveOperationException
     */
    public function move(Chapter $chapter, string $parentIdentifier): Recipe
    {
        $stringExploded = explode(':', $parentIdentifier);
        $entityType = $stringExploded[0];
        $entityId = intval($stringExploded[1]);

        if ($entityType !== 'book') {
            throw new MoveOperationException('Chapters can only be moved into books');
        }

        /** @var Recipe $parent */
        $parent = Recipe::visible()->where('id', '=', $entityId)->first();
        if ($parent === null) {
            throw new MoveOperationException('Recipe to move chapter into not found');
        }

        $chapter->changeRecipe($parent->id);
        $chapter->rebuildPermissions();
        Activity::addForEntity($chapter, ActivityType::CHAPTER_MOVE);

        return $parent;
    }
}
