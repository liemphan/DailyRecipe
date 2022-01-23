<?php

namespace DailyRecipe\Http\Controllers;

use DailyRecipe\Entities\Repos\RecipeRepo;
use DailyRecipe\Entities\Tools\RecipeContents;
use DailyRecipe\Exceptions\NotFoundException;
use Ssddanbrown\HtmlDiff\Diff;

class RecipeRevisionController extends Controller
{
    protected $recipeRepo;

    /**
     * RecipeRevisionController constructor.
     */
    public function __construct(RecipeRepo $recipeRepo)
    {
        $this->recipeRepo = $recipeRepo;
    }

    /**
     * Shows the last revisions for this page.
     *
     * @throws NotFoundException
     */
    public function index(string $recipeSlug)
    {
        $recipe = $this->recipeRepo->getBySlug($recipeSlug);
        $this->setPageTitle(trans('entities.pages_revisions_named', ['pageName' => $recipe->getShortName()]));

        return view('pages.revisions', [
            'recipe' => $recipe,
            'current' => $recipe,
        ]);
    }

    /**
     * Shows a preview of a single revision.
     *
     * @throws NotFoundException
     */
    public function show(string $recipeSlug, int $revisionId)
    {
        $recipe = $this->recipeRepo->getBySlug($recipeSlug);
        $revision = $recipe->revisions()->where('id', '=', $revisionId)->first();
        if ($revision === null) {
            throw new NotFoundException();
        }

        $recipe->fill($revision->toArray());
        // TODO - Refactor recipeContent so we don't need to juggle this
        $recipe->html = $revision->html;
        $recipe->html = (new RecipeContents($recipe))->render();

        $this->setPageTitle(trans('entities.pages_revision_named', ['pageName' => $recipe->getShortName()]));

        return view('pages.revision', [
            'recipe' => $recipe,
            'diff' => null,
            'revision' => $revision,
        ]);
    }

    /**
     * Shows the changes of a single revision.
     *
     * @throws NotFoundException
     */
    public function changes(string $recipeSlug, int $revisionId)
    {
        $recipe = $this->recipeRepo->getBySlug($recipeSlug);
        $revision = $recipe->revisions()->where('id', '=', $revisionId)->first();
        if ($revision === null) {
            throw new NotFoundException();
        }

        $prev = $revision->getPrevious();
        $prevContent = $prev->html ?? '';
        $diff = Diff::excecute($prevContent, $revision->html);

        $recipe->fill($revision->toArray());
        // TODO - Refactor RecipeContents so we don't need to juggle this
        $recipe->html = $revision->html;
        $recipe->html = (new RecipeContents($recipe))->render();
        $this->setPageTitle(trans('entities.pages_revision_named', ['pageName' => $recipe->getShortName()]));

        return view('pages.revision', [
            'recipe' => $recipe,
            'diff' => $diff,
            'revision' => $revision,
        ]);
    }

    /**
     * Restores a page using the content of the specified revision.
     *
     * @throws NotFoundException
     */
    public function restore(string $recipeSlug, int $revisionId)
    {
        $recipe = $this->recipeRepo->getBySlug($recipeSlug);
        $this->checkOwnablePermission('page-update', $recipe);

        $recipe = $this->recipeRepo->restoreRevision($recipe, $revisionId);

        return redirect($recipe->getUrl());
    }

    /**
     * Deletes a revision using the id of the specified revision.
     *
     * @throws NotFoundException
     */
    public function destroy(string $recipeSlug, int $revId)
    {
        $recipe = $this->recipeRepo->getBySlug($recipeSlug);
        $this->checkOwnablePermission('page-delete', $recipe);

        $revision = $recipe->revisions()->where('id', '=', $revId)->first();
        if ($revision === null) {
            throw new NotFoundException("Revision #{$revId} not found");
        }

        // Get the current revision for the page
        $currentRevision = $recipe->getCurrentRevision();

        // Check if its the latest revision, cannot delete latest revision.
        if (intval($currentRevision->id) === intval($revId)) {
            $this->showErrorNotification(trans('entities.revision_cannot_delete_latest'));

            return redirect($recipe->getUrl('/revisions'));
        }

        $revision->delete();
        $this->showSuccessNotification(trans('entities.revision_delete_success'));

        return redirect($recipe->getUrl('/revisions'));
    }
}
