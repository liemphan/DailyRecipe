<?php

namespace DailyRecipe\Http\Controllers;

use DailyRecipe\Actions\ActivityType;
use DailyRecipe\Entities\Models\Recipe;
use DailyRecipe\Entities\Repos\RecipeRepo;
use DailyRecipe\Entities\Tools\RecipeContents;
use DailyRecipe\Exceptions\SortOperationException;
use DailyRecipe\Facades\Activity;
use Illuminate\Http\Request;

class RecipeSortController extends Controller
{
    protected $recipeRepo;

    public function __construct(RecipeRepo $recipeRepo)
    {
        $this->recipeRepo = $recipeRepo;
    }

    /**
     * Shows the view which allows pages to be re-ordered and sorted.
     */
    public function show(string $recipeSlug)
    {
        $recipe = $this->recipeRepo->getBySlug($recipeSlug);
        $this->checkOwnablePermission('recipe-update', $recipe);

        $recipeChildren = (new RecipeContents($recipe))->getTree(false);

        $this->setPageTitle(trans('entities.recipes_sort_named', ['recipeName' => $recipe->getShortName()]));

        return view('recipes.sort', ['recipe' => $recipe, 'current' => $recipe, 'recipeChildren' => $recipeChildren]);
    }

    /**
     * Shows the sort box for a single recipe.
     * Used via AJAX when loading in extra recipes to a sort.
     */
    public function showItem(string $recipeSlug)
    {
        $recipe = $this->recipeRepo->getBySlug($recipeSlug);
        $recipeChildren = (new RecipeContents($recipe))->getTree();

        return view('recipes.parts.sort-box', ['recipe' => $recipe, 'recipeChildren' => $recipeChildren]);
    }

    /**
     * Sorts a recipe using a given mapping array.
     */
    public function update(Request $request, string $recipeSlug)
    {
        $recipe = $this->recipeRepo->getBySlug($recipeSlug);
        $this->checkOwnablePermission('recipe-update', $recipe);

        // Return if no map sent
        if (!$request->filled('sort-tree')) {
            return redirect($recipe->getUrl());
        }

        $sortMap = collect(json_decode($request->get('sort-tree')));
        $recipeContents = new RecipeContents($recipe);
        $recipesInvolved = collect();

        try {
            $recipesInvolved = $recipeContents->sortUsingMap($sortMap);
        } catch (SortOperationException $exception) {
            $this->showPermissionError();
        }

        // Rebuild permissions and add activity for involved recipes.
        $recipesInvolved->each(function (Recipe $recipe) {
            Activity::addForEntity($recipe, ActivityType::RECIPE_SORT);
        });

        return redirect($recipe->getUrl());
    }
}
