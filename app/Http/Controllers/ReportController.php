<?php

namespace DailyRecipe\Http\Controllers;

use DailyRecipe\Actions\Report;
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
     */
    public function store(Request $request, string $recipeSlug=null)
    {

        $recipe = $this->recipeRepo->getBySlug($recipeSlug);
        $page = Recipe::visible()->find($recipe->id);

        if ($page === null) {
            return response('Not found', 404);
        }
        $report = $this->reportRepo->create($request->get('name'), $request->get('description'),$page);
        $this->showSuccessNotification(trans('entities.recipes_reports'));
        return redirect($recipe->getUrl());
    }
}