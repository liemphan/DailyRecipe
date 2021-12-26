<?php

namespace DailyRecipe\Http\Controllers;

use DailyRecipe\Actions\View;
use DailyRecipe\Entities\Models\Recipe;
use DailyRecipe\Entities\Repos\ChapterRepo;
use DailyRecipe\Entities\Tools\RecipeContents;
use DailyRecipe\Entities\Tools\NextPreviousContentLocator;
use DailyRecipe\Entities\Tools\PermissionsUpdater;
use DailyRecipe\Exceptions\MoveOperationException;
use DailyRecipe\Exceptions\NotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Throwable;

class ChapterController extends Controller
{
    protected $chapterRepo;

    /**
     * ChapterController constructor.
     */
    public function __construct(ChapterRepo $chapterRepo)
    {
        $this->chapterRepo = $chapterRepo;
    }

    /**
     * Show the form for creating a new chapter.
     */
    public function create(string $recipeSlug)
    {
        $recipe = Recipe::visible()->where('slug', '=', $recipeSlug)->firstOrFail();
        $this->checkOwnablePermission('chapter-create', $recipe);

        $this->setPageTitle(trans('entities.chapters_create'));

        return view('chapters.create', ['recipe' => $recipe, 'current' => $recipe]);
    }

    /**
     * Store a newly created chapter in storage.
     *
     * @throws ValidationException
     */
    public function store(Request $request, string $recipeSlug)
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'max:255'],
        ]);

        $recipe = Recipe::visible()->where('slug', '=', $recipeSlug)->firstOrFail();
        $this->checkOwnablePermission('chapter-create', $recipe);

        $chapter = $this->chapterRepo->create($request->all(), $recipe);

        return redirect($chapter->getUrl());
    }

    /**
     * Display the specified chapter.
     */
    public function show(string $recipeSlug, string $chapterSlug)
    {
        $chapter = $this->chapterRepo->getBySlug($recipeSlug, $chapterSlug);
        $this->checkOwnablePermission('chapter-view', $chapter);

        $sidebarTree = (new RecipeContents($chapter->recipe))->getTree();
        $pages = $chapter->getVisiblePages();
        $nextPreviousLocator = new NextPreviousContentLocator($chapter, $sidebarTree);
        View::incrementFor($chapter);

        $this->setPageTitle($chapter->getShortName());

        return view('chapters.show', [
            'recipe'        => $chapter->recipe,
            'chapter'     => $chapter,
            'current'     => $chapter,
            'sidebarTree' => $sidebarTree,
            'pages'       => $pages,
            'next'        => $nextPreviousLocator->getNext(),
            'previous'    => $nextPreviousLocator->getPrevious(),
        ]);
    }

    /**
     * Show the form for editing the specified chapter.
     */
    public function edit(string $recipeSlug, string $chapterSlug)
    {
        $chapter = $this->chapterRepo->getBySlug($recipeSlug, $chapterSlug);
        $this->checkOwnablePermission('chapter-update', $chapter);

        $this->setPageTitle(trans('entities.chapters_edit_named', ['chapterName' => $chapter->getShortName()]));

        return view('chapters.edit', ['recipe' => $chapter->recipe, 'chapter' => $chapter, 'current' => $chapter]);
    }

    /**
     * Update the specified chapter in storage.
     *
     * @throws NotFoundException
     */
    public function update(Request $request, string $recipeSlug, string $chapterSlug)
    {
        $chapter = $this->chapterRepo->getBySlug($recipeSlug, $chapterSlug);
        $this->checkOwnablePermission('chapter-update', $chapter);

        $this->chapterRepo->update($chapter, $request->all());

        return redirect($chapter->getUrl());
    }

    /**
     * Shows the page to confirm deletion of this chapter.
     *
     * @throws NotFoundException
     */
    public function showDelete(string $recipeSlug, string $chapterSlug)
    {
        $chapter = $this->chapterRepo->getBySlug($recipeSlug, $chapterSlug);
        $this->checkOwnablePermission('chapter-delete', $chapter);

        $this->setPageTitle(trans('entities.chapters_delete_named', ['chapterName' => $chapter->getShortName()]));

        return view('chapters.delete', ['recipe' => $chapter->recipe, 'chapter' => $chapter, 'current' => $chapter]);
    }

    /**
     * Remove the specified chapter from storage.
     *
     * @throws NotFoundException
     * @throws Throwable
     */
    public function destroy(string $recipeSlug, string $chapterSlug)
    {
        $chapter = $this->chapterRepo->getBySlug($recipeSlug, $chapterSlug);
        $this->checkOwnablePermission('chapter-delete', $chapter);

        $this->chapterRepo->destroy($chapter);

        return redirect($chapter->recipe->getUrl());
    }

    /**
     * Show the page for moving a chapter.
     *
     * @throws NotFoundException
     */
    public function showMove(string $recipeSlug, string $chapterSlug)
    {
        $chapter = $this->chapterRepo->getBySlug($recipeSlug, $chapterSlug);
        $this->setPageTitle(trans('entities.chapters_move_named', ['chapterName' => $chapter->getShortName()]));
        $this->checkOwnablePermission('chapter-update', $chapter);
        $this->checkOwnablePermission('chapter-delete', $chapter);

        return view('chapters.move', [
            'chapter' => $chapter,
            'recipe'    => $chapter->recipe,
        ]);
    }

    /**
     * Perform the move action for a chapter.
     *
     * @throws NotFoundException
     */
    public function move(Request $request, string $recipeSlug, string $chapterSlug)
    {
        $chapter = $this->chapterRepo->getBySlug($recipeSlug, $chapterSlug);
        $this->checkOwnablePermission('chapter-update', $chapter);
        $this->checkOwnablePermission('chapter-delete', $chapter);

        $entitySelection = $request->get('entity_selection', null);
        if ($entitySelection === null || $entitySelection === '') {
            return redirect($chapter->getUrl());
        }

        try {
            $newRecipe = $this->chapterRepo->move($chapter, $entitySelection);
        } catch (MoveOperationException $exception) {
            $this->showErrorNotification(trans('errors.selected_recipe_not_found'));

            return redirect()->back();
        }

        $this->showSuccessNotification(trans('entities.chapter_move_success', ['recipeName' => $newRecipe->name]));

        return redirect($chapter->getUrl());
    }

    /**
     * Show the Restrictions view.
     *
     * @throws NotFoundException
     */
    public function showPermissions(string $recipeSlug, string $chapterSlug)
    {
        $chapter = $this->chapterRepo->getBySlug($recipeSlug, $chapterSlug);
        $this->checkOwnablePermission('restrictions-manage', $chapter);

        return view('chapters.permissions', [
            'chapter' => $chapter,
        ]);
    }

    /**
     * Set the restrictions for this chapter.
     *
     * @throws NotFoundException
     */
    public function permissions(Request $request, PermissionsUpdater $permissionsUpdater, string $recipeSlug, string $chapterSlug)
    {
        $chapter = $this->chapterRepo->getBySlug($recipeSlug, $chapterSlug);
        $this->checkOwnablePermission('restrictions-manage', $chapter);

        $permissionsUpdater->updateFromPermissionsForm($chapter, $request);

        $this->showSuccessNotification(trans('entities.chapters_permissions_success'));

        return redirect($chapter->getUrl());
    }
}
