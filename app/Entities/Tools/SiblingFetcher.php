<?php

namespace DailyRecipe\Entities\Tools;

use DailyRecipe\Entities\EntityProvider;
use DailyRecipe\Entities\Models\Recipe;
use DailyRecipe\Entities\Models\RecipeMenu;

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

//        // Page in chapter
//        if ($entity instanceof Page && $entity->chapter) {
//            $entities = $entity->chapter->getVisiblePages();
//        }
//
//        // Page in recipe or chapter
//        if (($entity instanceof Page && !$entity->chapter) || $entity instanceof Chapter) {
//            $entities = $entity->recipe->getDirectChildren();
//        }

        // Recipe
        // Gets just the recipes in a menu if menu is in context
        if ($entity instanceof Recipe) {
            $contextMenu = (new MenuContext())->getContextualMenuForRecipe($entity);
            if ($contextMenu) {
                $entities = $contextMenu->visibleRecipes()->get();
            } else {
                $entities = Recipe::visible()->get();
            }
        }

        // Menu
        if ($entity instanceof RecipeMenu) {
            $entities = RecipeMenu::visible()->get();
        }

        return $entities;
    }
}
