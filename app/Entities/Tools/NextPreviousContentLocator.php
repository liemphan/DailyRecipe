<?php

namespace DailyRecipe\Entities\Tools;

use DailyRecipe\Entities\Models\Recipe;
use DailyRecipe\Entities\Models\RecipeChild;
use DailyRecipe\Entities\Models\Entity;
use Illuminate\Support\Collection;

/**
 * Finds the next or previous content of a recipe element (page or chapter).
 */
class NextPreviousContentLocator
{
    protected $relativeRecipeItem;
    protected $flatTree;
    protected $currentIndex = null;

    /**
     * NextPreviousContentLocator constructor.
     */
    public function __construct(Recipe $relativeRecipeItem)
    {
        $this->relativeRecipeItem = $relativeRecipeItem;
//        $this->flatTree = $this->treeToFlatOrderedCollection($recipeTree);
        $this->currentIndex = $this->getCurrentIndex();
    }

    /**
     * Get the next logical entity within the recipe hierarchy.
     */
    public function getNext(): ?Entity
    {
        return $this->flatTree->get($this->currentIndex + 1);
    }

    /**
     * Get the next logical entity within the recipe hierarchy.
     */
    public function getPrevious(): ?Entity
    {
        return $this->flatTree->get($this->currentIndex - 1);
    }

    /**
     * Get the index of the current relative item.
     */
    protected function getCurrentIndex(): ?int
    {
        $index = $this->flatTree->search(function (Entity $entity) {
            return get_class($entity) === get_class($this->relativeRecipeItem)
                && $entity->id === $this->relativeRecipeItem->id;
        });

        return $index === false ? null : $index;
    }

//    /**
//     * Convert a recipe tree collection to a flattened version
//     * where all items follow the expected order of user flow.
//     */
//    protected function treeToFlatOrderedCollection(Collection $recipeTree): Collection
//    {
//        $flatOrdered = collect();
//        /** @var Entity $item */
//        foreach ($recipeTree->all() as $item) {
//            $flatOrdered->push($item);
//            $childPages = $item->getAttribute('visible_pages') ?? [];
//            $flatOrdered = $flatOrdered->concat($childPages);
//        }
//
//        return $flatOrdered;
//    }
}
