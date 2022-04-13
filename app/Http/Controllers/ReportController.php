<?php

namespace DailyRecipe\Http\Controllers;

use DailyRecipe\Actions\Report;
use DailyRecipe\Entities\Models\Deletion;
use DailyRecipe\Entities\Models\Entity;
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
    public function reportList(Request $request)
    {
        // $this->checkPermission('settings-manage');
        $this->checkPermission('users-manage');

        $listDetails = [
            'order' => $request->get('order', 'desc'),
            'content' => $request->get('content', ''),
            'description' => $request->get('description', ''),
            'sort' => $request->get('sort', 'created_at'),
            'date_from' => $request->get('date_from', ''),
            'date_to' => $request->get('date_to', ''),
            'user' => $request->get('user', ''),
            'recipe' => $request->get('recipe', ''),
        ];

        $query = Report::query()
            ->with([
                'entity' => function ($query) {
                    $query->withTrashed();
                },
                'user',
            ])
            ->orderBy($listDetails['sort'], $listDetails['order']);

        if ($listDetails['content']) {
            $query->where('content', '=', $listDetails['content']);
        }
        if ($listDetails['description']) {
            $query->where('description', '=', $listDetails['description']);
        }
        if ($listDetails['user']) {
            $query->where('user_id', '=', $listDetails['user']);
        }

        if ($listDetails['date_from']) {
            $query->where('created_at', '>=', $listDetails['date_from']);
        }
        if ($listDetails['date_to']) {
            $query->where('created_at', '<=', $listDetails['date_to']);
        }
        if ($listDetails['recipe']) {
            $query->where('entity_id', '=', $listDetails['recipe']);
        }
        $reports = $query->paginate(100);
        $reports->appends($listDetails);

        // $types = DB::table('reports')->select('type')->distinct()->pluck('type');
        $this->setPageTitle(trans('settings.reportlist'));

        return view('recipes.report_list', [
            'reports' => $reports,
            'listDetails' => $listDetails,
            // 'activityTypes' => $types,
        ]);
    }
    /**
     * Show the page to confirm a restore of the deletion of the given id.
     */
    public function showRestore(string $id)
    {
        /** @var Deletion $deletion */
        $deletion = Deletion::query()->findOrFail($id,);

        // Walk the parent chain to find any cascading parent deletions
        $currentDeletable = $deletion->deletable;
        $searching = true;
        while ($searching && $currentDeletable instanceof Entity) {
//            $parent = $currentDeletable->getParent();
//            if ($parent && $parent->trashed()) {
//                $currentDeletable = $parent;
//            } else {
            $searching = false;
//            }
        }

        /** @var ?Deletion $parentDeletion */
        $parentDeletion = ($currentDeletable === $deletion->deletable) ? null : $currentDeletable->deletions()->first();

        return view('settings.recycle-bin.restore', [
            'deletion' => $deletion,
            'parentDeletion' => $parentDeletion,
        ]);
    }
    /**
     * Shows the page to confirm deletion.
     */
    public function showDelete(Request $request,string $recipeSlug, int $id)
    {
        $recipe = $this->recipeRepo->getBySlug($recipeSlug);
//        $report = $this->reportRepo->getById($id);
//        $report = $this->reportRepo->update($report);
        $this->checkOwnablePermission('recipe-delete', $recipe);
        $this->setPageTitle(trans('entities.recipes_delete_named', ['recipeName' => $recipe->getShortName()]));

        return view('recipes.deactive', ['recipe' => $recipe, 'current' => $recipe]);
    }
}