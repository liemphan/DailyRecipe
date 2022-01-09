<?php

namespace DailyRecipe\Http\Controllers;

use DailyRecipe\Entities\Repos\PageRepo;
use DailyRecipe\Entities\Tools\RecipeContents;
use DailyRecipe\Exceptions\NotFoundException;
use Ssddanbrown\HtmlDiff\Diff;

class RecipeRevisionController extends Controller
{
    protected $pageRepo;

    /**
     * RecipeRevisionController constructor.
     */
    public function __construct(PageRepo $pageRepo)
    {
        $this->pageRepo = $pageRepo;
    }

    /**
     * Shows the last revisions for this page.
     *
     * @throws NotFoundException
     */
    public function index(string $recipeSlug, string $pageSlug)
    {
        $page = $this->pageRepo->getBySlug($recipeSlug, $pageSlug);
        $this->setPageTitle(trans('entities.pages_revisions_named', ['pageName' => $page->getShortName()]));

        return view('pages.revisions', [
            'page' => $page,
            'current' => $page,
        ]);
    }

    /**
     * Shows a preview of a single revision.
     *
     * @throws NotFoundException
     */
    public function show(string $recipeSlug, string $pageSlug, int $revisionId)
    {
        $page = $this->pageRepo->getBySlug($recipeSlug, $pageSlug);
        $revision = $page->revisions()->where('id', '=', $revisionId)->first();
        if ($revision === null) {
            throw new NotFoundException();
        }

        $page->fill($revision->toArray());
        // TODO - Refactor recipeContent so we don't need to juggle this
        $page->html = $revision->html;
        $page->html = (new RecipeContents($page))->render();

        $this->setPageTitle(trans('entities.pages_revision_named', ['pageName' => $page->getShortName()]));

        return view('pages.revision', [
            'page' => $page,
            'recipe' => $page->recipe,
            'diff' => null,
            'revision' => $revision,
        ]);
    }

    /**
     * Shows the changes of a single revision.
     *
     * @throws NotFoundException
     */
    public function changes(string $recipeSlug, string $pageSlug, int $revisionId)
    {
        $page = $this->pageRepo->getBySlug($recipeSlug, $pageSlug);
        $revision = $page->revisions()->where('id', '=', $revisionId)->first();
        if ($revision === null) {
            throw new NotFoundException();
        }

        $prev = $revision->getPrevious();
        $prevContent = $prev->html ?? '';
        $diff = Diff::excecute($prevContent, $revision->html);

        $page->fill($revision->toArray());
        // TODO - Refactor RecipeContents so we don't need to juggle this
        $page->html = $revision->html;
        $page->html = (new RecipeContents($page))->render();
        $this->setPageTitle(trans('entities.pages_revision_named', ['pageName' => $page->getShortName()]));

        return view('pages.revision', [
            'page' => $page,
            'recipe' => $page->recipe,
            'diff' => $diff,
            'revision' => $revision,
        ]);
    }

    /**
     * Restores a page using the content of the specified revision.
     *
     * @throws NotFoundException
     */
    public function restore(string $recipeSlug, string $pageSlug, int $revisionId)
    {
        $page = $this->pageRepo->getBySlug($recipeSlug, $pageSlug);
        $this->checkOwnablePermission('page-update', $page);

        $page = $this->pageRepo->restoreRevision($page, $revisionId);

        return redirect($page->getUrl());
    }

    /**
     * Deletes a revision using the id of the specified revision.
     *
     * @throws NotFoundException
     */
    public function destroy(string $recipeSlug, string $pageSlug, int $revId)
    {
        $page = $this->pageRepo->getBySlug($recipeSlug, $pageSlug);
        $this->checkOwnablePermission('page-delete', $page);

        $revision = $page->revisions()->where('id', '=', $revId)->first();
        if ($revision === null) {
            throw new NotFoundException("Revision #{$revId} not found");
        }

        // Get the current revision for the page
        $currentRevision = $page->getCurrentRevision();

        // Check if its the latest revision, cannot delete latest revision.
        if (intval($currentRevision->id) === intval($revId)) {
            $this->showErrorNotification(trans('entities.revision_cannot_delete_latest'));

            return redirect($page->getUrl('/revisions'));
        }

        $revision->delete();
        $this->showSuccessNotification(trans('entities.revision_delete_success'));

        return redirect($page->getUrl('/revisions'));
    }
}
