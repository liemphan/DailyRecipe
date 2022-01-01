<?php

namespace DailyRecipe\Entities\Tools;

use DailyRecipe\Entities\Models\Recipe;
use DailyRecipe\Entities\Models\RecipeChild;
use DailyRecipe\Entities\Models\Chapter;
use DailyRecipe\Entities\Models\Entity;
use DailyRecipe\Entities\Models\Page;
use DailyRecipe\Exceptions\SortOperationException;
use Illuminate\Support\Collection;
use stdClass;

class RecipeContents
{
    /**
     * @var Recipe
     */
    protected $recipe;

    /**
     * RecipeContents constructor.
     */
    public function __construct(Recipe $recipe)
    {
        $this->recipe = $recipe;
    }

    /**
     * Get the current priority of the last item
     * at the top-level of the recipe.
     */
    public function getLastPriority(): int
    {
        $maxPage = Page::visible()->where('recipe_id', '=', $this->recipe->id)
            ->where('draft', '=', false)
            ->where('chapter_id', '=', 0)->max('priority');
        $maxChapter = Chapter::visible()->where('recipe_id', '=', $this->recipe->id)
            ->max('priority');

        return max($maxChapter, $maxPage, 1);
    }

//    /**
//     * Get the contents as a sorted collection tree.
//     */
//    public function getTree(bool $showDrafts = false, bool $renderPages = false): Collection
//    {
//        $pages = $this->getPages($showDrafts, $renderPages);
//        $chapters = Chapter::visible()->where('recipe_id', '=', $this->recipe->id)->get();
//        $all = collect()->concat($pages)->concat($chapters);
//        $chapterMap = $chapters->keyBy('id');
//        $lonePages = collect();
//
//        $pages->groupBy('chapter_id')->each(function ($pages, $chapter_id) use ($chapterMap, &$lonePages) {
//            $chapter = $chapterMap->get($chapter_id);
//            if ($chapter) {
//                $chapter->setAttribute('visible_pages', collect($pages)->sortBy($this->recipeChildSortFunc()));
//            } else {
//                $lonePages = $lonePages->concat($pages);
//            }
//        });
//
//        $chapters->whereNull('visible_pages')->each(function (Chapter $chapter) {
//            $chapter->setAttribute('visible_pages', collect([]));
//        });
//
//        $all->each(function (Entity $entity) use ($renderPages) {
//            $entity->setRelation('recipe', $this->recipe);
//
//            if ($renderPages && $entity instanceof Page) {
//                $entity->html = (new PageContent($entity))->render();
//            }
//        });
//
//        return collect($chapters)->concat($lonePages)->sortBy($this->recipeChildSortFunc());
//    }

    /**
     * Function for providing a sorting score for an entity in relation to the
     * other items within the recipe.
     */
    protected function recipeChildSortFunc(): callable
    {
        return function (Entity $entity) {
            if (isset($entity['draft']) && $entity['draft']) {
                return -100;
            }

            return $entity['priority'] ?? 0;
        };
    }

    /**
     * Get the visible pages within this recipe.
     */
    protected function getPages(bool $showDrafts = false, bool $getPageContent = false): Collection
    {
        $query = Page::visible()
            ->select($getPageContent ? Page::$contentAttributes : Page::$listAttributes)
            ->where('recipe_id', '=', $this->recipe->id);

        if (!$showDrafts) {
            $query->where('draft', '=', false);
        }

        return $query->get();
    }

    /**
     * Sort the recipes content using the given map.
     * The map is a single-dimension collection of objects in the following format:
     *   {
     *     +"id": "294" (ID of item)
     *     +"sort": 1 (Sort order index)
     *     +"parentChapter": false (ID of parent chapter, as string, or false)
     *     +"type": "page" (Entity type of item)
     *     +"recipe": "1" (Id of recipe to place item in)
     *   }.
     *
     * Returns a list of recipes that were involved in the operation.
     *
     * @throws SortOperationException
     */
    public function sortUsingMap(Collection $sortMap): Collection
    {
        // Load models into map
        $this->loadModelsIntoSortMap($sortMap);
        $recipesInvolved = $this->getRecipesInvolvedInSort($sortMap);

        // Perform the sort
        $sortMap->each(function ($mapItem) {
            $this->applySortUpdates($mapItem);
        });

        // Update permissions and activity.
        $recipesInvolved->each(function (Recipe $recipe) {
            $recipe->rebuildPermissions();
        });

        return $recipesInvolved;
    }

    /**
     * Using the given sort map item, detect changes for the related model
     * and update it if required.
     */
    protected function applySortUpdates(stdClass $sortMapItem)
    {
        /** @var RecipeChild $model */
        $model = $sortMapItem->model;

        $priorityChanged = intval($model->priority) !== intval($sortMapItem->sort);
        $recipeChanged = intval($model->recipe_id) !== intval($sortMapItem->recipe);
        $chapterChanged = ($model instanceof Page) && intval($model->chapter_id) !== $sortMapItem->parentChapter;

        if ($recipeChanged) {
            $model->changeRecipe($sortMapItem->recipe);
        }

        if ($chapterChanged) {
            $model->chapter_id = intval($sortMapItem->parentChapter);
            $model->save();
        }

        if ($priorityChanged) {
            $model->priority = intval($sortMapItem->sort);
            $model->save();
        }
    }

    /**
     * Load models from the database into the given sort map.
     */
    protected function loadModelsIntoSortMap(Collection $sortMap): void
    {
        $keyMap = $sortMap->keyBy(function (stdClass $sortMapItem) {
            return  $sortMapItem->type . ':' . $sortMapItem->id;
        });
        $pageIds = $sortMap->where('type', '=', 'page')->pluck('id');
        $chapterIds = $sortMap->where('type', '=', 'chapter')->pluck('id');

        $pages = Page::visible()->whereIn('id', $pageIds)->get();
        $chapters = Chapter::visible()->whereIn('id', $chapterIds)->get();

        foreach ($pages as $page) {
            $sortItem = $keyMap->get('page:' . $page->id);
            $sortItem->model = $page;
        }

        foreach ($chapters as $chapter) {
            $sortItem = $keyMap->get('chapter:' . $chapter->id);
            $sortItem->model = $chapter;
        }
    }

    /**
     * Get the recipes involved in a sort.
     * The given sort map should have its models loaded first.
     *
     * @throws SortOperationException
     */
    protected function getRecipesInvolvedInSort(Collection $sortMap): Collection
    {
        $recipeIdsInvolved = collect([$this->recipe->id]);
        $recipeIdsInvolved = $recipeIdsInvolved->concat($sortMap->pluck('recipe'));
        $recipeIdsInvolved = $recipeIdsInvolved->concat($sortMap->pluck('model.recipe_id'));
        $recipeIdsInvolved = $recipeIdsInvolved->unique()->toArray();

        $recipes = Recipe::hasPermission('update')->whereIn('id', $recipeIdsInvolved)->get();

        if (count($recipes) !== count($recipeIdsInvolved)) {
            throw new SortOperationException('Could not find all recipes requested in sort operation');
        }

        return $recipes;
    }
}
