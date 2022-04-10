<?php

namespace DailyRecipe\Http\Controllers;

use DailyRecipe\Entities\Models\Recipe;
use Illuminate\Http\Request;
use DailyRecipe\Actions\ReportRepo;
use DailyRecipe\Entities\Repos\RecipeRepo;

class ReportController extends Controller
{
    protected $reportRepo;
    protected $recipeRepo;
    public function __construct(ReportRepo $reportRepo, RecipeRepo $recipeRepo)
    {
        $this->reportRepo = $reportRepo;
        $this->recipeRepo = $recipeRepo;
    }

    /**
     * Show the report view.
     */
    public function showReport(string $recipeSlug)
    {
        $recipe = $this->recipeRepo->getBySlug($recipeSlug);

        return view('recipes.report', [
             'recipe' => $recipe,
        ]);
    }

    /**
     * Set the restrictions for this recipe.
     *
     * @throws Throwable
     */
    public function store(Request $request, string $recipeSlug)
    {
        $page = Recipe::visible()->find($recipeSlug);
        $recipe = $this->recipeRepo->getBySlug($recipeSlug);
        //$this->checkOwnablePermission('restrictions-manage', $recipe);


        $report = $this->reportRepo->create($recipe, $request->get('name'), $request->get('description'));

        $this->showSuccessNotification(trans('entities.recipes_reports'));

        //return redirect($recipe->getUrl());
        return redirect($report->getUrlContent());;
    }
}