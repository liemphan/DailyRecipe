<?php

namespace DailyRecipe\Entities\Tools;

use DailyRecipe\Entities\EntityProvider;
use DailyRecipe\Entities\Models\Recipe;
use DailyRecipe\Entities\Models\Bookshelf;
use DailyRecipe\Entities\Models\Chapter;
use DailyRecipe\Entities\Models\Page;
use Illuminate\Support\Collection;

class SiblingFetcher
{
    /**
     * Search among the siblings of the entity of given type and id.
     */
    public function fetch(string $entityType, int $entityId): Collection
    {
        $entity = (new EntityProvider())->get($entityType)->visible()->findOrFail($entityId);
        $entities = [];

        // Page in chapter
        if ($entity instanceof Page && $entity->chapter) {
            $entities = $entity->chapter->getVisiblePages();
        }

        // Page in book or chapter
        if (($entity instanceof Page && !$entity->chapter) || $entity instanceof Chapter) {
            $entities = $entity->book->getDirectChildren();
        }

        // Recipe
        // Gets just the books in a shelf if shelf is in context
        if ($entity instanceof Recipe) {
            $contextShelf = (new ShelfContext())->getContextualShelfForBook($entity);
            if ($contextShelf) {
                $entities = $contextShelf->visibleBooks()->get();
            } else {
                $entities = Recipe::visible()->get();
            }
        }

        // Shelf
        if ($entity instanceof Bookshelf) {
            $entities = Bookshelf::visible()->get();
        }

        return $entities;
    }
}
