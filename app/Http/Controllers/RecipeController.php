<?php

namespace DailyRecipe\Http\Controllers;

use Activity;
use DailyRecipe\Actions\ActivityType;
use DailyRecipe\Actions\View;
use DailyRecipe\Entities\Models\Recipe;
use DailyRecipe\Entities\Models\Recipemenu;
use DailyRecipe\Entities\Repos\PageRepo;
use DailyRecipe\Entities\Repos\RecipeRepo;
use DailyRecipe\Entities\Tools\NextPreviousContentLocator;
use DailyRecipe\Entities\Tools\PageContent;
use DailyRecipe\Entities\Tools\PageEditActivity;
use DailyRecipe\Entities\Tools\RecipeContents;
use DailyRecipe\Entities\Tools\PermissionsUpdater;
use DailyRecipe\Entities\Tools\MenuContext;
use DailyRecipe\Exceptions\ImageUploadException;
use DailyRecipe\Exceptions\NotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Throwable;

class RecipeController extends Controller
{
    protected $recipeRepo;
    protected $entityContextManager;

    public function __construct(MenuContext $entityContextManager, RecipeRepo $recipeRepo)
    {
        $this->recipeRepo = $recipeRepo;
        $this->entityContextManager = $entityContextManager;
    }

    /**
     * Display a listing of the recipe.
     */
    public function index()
    {
        $view = setting()->getForCurrentUser('recipes_view_type');
        $sort = setting()->getForCurrentUser('recipes_sort', 'name');
        $order = setting()->getForCurrentUser('recipes_sort_order', 'asc');

        $recipes = $this->recipeRepo->getAllPaginated(18, $sort, $order);
        $recents = $this->isSignedIn() ? $this->recipeRepo->getRecentlyViewed(4) : false;
        $popular = $this->recipeRepo->getPopular(4);
        $new = $this->recipeRepo->getRecentlyCreated(4);

        $this->entityContextManager->clearMenuContext();

        $this->setPageTitle(trans('entities.recipes'));

        return view('recipes.index', [
            'recipes' => $recipes,
            'recents' => $recents,
            'popular' => $popular,
            'new' => $new,
            'view' => $view,
            'sort' => $sort,
            'order' => $order,
        ]);
    }

    /**
     * Show the form for creating a new recipe.
     */
    public function create(string $menuSlug = null)
    {
        $this->checkPermission('recipe-create-all');

        $recipemenu = null;
        if ($menuSlug !== null) {
            $recipemenu = Recipemenu::visible()->where('slug', '=', $menuSlug)->firstOrFail();
            $this->checkOwnablePermission('recipemenu-update', $recipemenu);
        }

        $this->setPageTitle(trans('entities.recipes_create'));

        return view('recipes.create', [
            'recipemenu' => $recipemenu,
        ]);
    }


//    public function store(Request $request, string $menuSlug = null)
//    {
//        $this->checkPermission('recipe-create-all');
//        $this->validate($request, [
//            'name'        => ['required', 'string', 'max:255'],
//            'description' => ['string', 'max:1000'],
//            'image'       => array_merge(['nullable'], $this->getImageValidationRules()),
//        ]);
//
//        $recipemenu = null;
//        if ($menuSlug !== null) {
//            $recipemenu = Recipemenu::visible()->where('slug', '=', $menuSlug)->firstOrFail();
//            $this->checkOwnablePermission('recipemenu-update', $recipemenu);
//        }
//
//        $recipe = $this->recipeRepo->create($request->all());
//        $this->recipeRepo->updateCoverImage($recipe, $request->file('image', null));
//
//        if ($recipemenu) {
//            $recipemenu->appendRecipe($recipe);
//            Activity::addForEntity($recipemenu, ActivityType::RECIPEMENU_UPDATE);
//        }
//
//        return redirect($recipe->getUrl('/create-page'));
//    }

    /**
     * Display the specified recipe.
     */
    public function show(Request $request, string $slug)
    {
        $recipe = $this->recipeRepo->getBySlug($slug);
        $recipeParentMenus = $recipe->menus()->scopes('visible')->get();

        View::incrementFor($recipe);
        if ($request->has('menu')) {
            $this->entityContextManager->setMenuContext(intval($request->get('menu')));
        }

        $this->setPageTitle($recipe->getShortName());

        return view('recipes.show', [
            'recipe' => $recipe,
            'current' => $recipe,
            'recipeParentMenus' => $recipeParentMenus,
            'activity' => Activity::entityActivity($recipe, 20, 1),
        ]);
    }

    /**
     * Show the form for editing the specified recipe.
     */
    public function edit(string $slug)
    {
        $recipe = $this->recipeRepo->getBySlug($slug);
        $this->checkOwnablePermission('recipe-update', $recipe);
        $this->setPageTitle(trans('entities.recipes_edit_named', ['recipeName' => $recipe->getShortName()]));

        return view('recipes.edit', ['recipe' => $recipe, 'current' => $recipe]);
    }

    /**
     * Update the specified recipe in storage.
     *
     * @throws ImageUploadException
     * @throws ValidationException
     * @throws Throwable
     */
    public function update(Request $request, string $slug)
    {
        $recipe = $this->recipeRepo->getBySlug($slug);
        $this->checkOwnablePermission('recipe-update', $recipe);
        $this->validate($request, [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['string', 'max:1000'],
            'image' => array_merge(['nullable'], $this->getImageValidationRules()),
        ]);

        $recipe = $this->recipeRepo->update($recipe, $request->all());
        $resetCover = $request->has('image_reset');
        $this->recipeRepo->updateCoverImage($recipe, $request->file('image', null), $resetCover);

        return redirect($recipe->getUrl());
    }

    /**
     * Shows the page to confirm deletion.
     */
    public function showDelete(string $recipeSlug)
    {
        $recipe = $this->recipeRepo->getBySlug($recipeSlug);
        $this->checkOwnablePermission('recipe-delete', $recipe);
        $this->setPageTitle(trans('entities.recipes_delete_named', ['recipeName' => $recipe->getShortName()]));

        return view('recipes.delete', ['recipe' => $recipe, 'current' => $recipe]);
    }

    /**
     * Remove the specified recipe from the system.
     *
     * @throws Throwable
     */
    public function destroy(string $recipeSlug)
    {
        $recipe = $this->recipeRepo->getBySlug($recipeSlug);
        $this->checkOwnablePermission('recipe-delete', $recipe);

        $this->recipeRepo->destroy($recipe);

        return redirect('/recipes');
    }

    /**
     * Show the permissions view.
     */
    public function showPermissions(string $recipeSlug)
    {
        $recipe = $this->recipeRepo->getBySlug($recipeSlug);
        $this->checkOwnablePermission('restrictions-manage', $recipe);

        return view('recipes.permissions', [
            'recipe' => $recipe,
        ]);
    }

    /**
     * Set the restrictions for this recipe.
     *
     * @throws Throwable
     */
    public function permissions(Request $request, PermissionsUpdater $permissionsUpdater, string $recipeSlug)
    {
        $recipe = $this->recipeRepo->getBySlug($recipeSlug);
        $this->checkOwnablePermission('restrictions-manage', $recipe);

        $permissionsUpdater->updateFromPermissionsForm($recipe, $request);

        $this->showSuccessNotification(trans('entities.recipes_permissions_updated'));

        return redirect($recipe->getUrl());
    }

    /**
     * Store a newly created recipe in storage.
     *
     * @throws ImageUploadException
     * @throws ValidationException
     */
    public function store(Request $request, string $menuSlug = null)
    {
        $this->checkPermission('recipe-create-all');
        $this->validate($request, [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['string', 'max:1000'],
            'image' => array_merge(['nullable'], $this->getImageValidationRules()),
        ]);

        $recipemenu = null;
        if ($menuSlug !== null) {
            $recipemenu = Recipemenu::visible()->where('slug', '=', $menuSlug)->firstOrFail();
            $this->checkOwnablePermission('recipemenu-update', $recipemenu);
        }

        $recipe = $this->recipeRepo->create($request->all());
        $this->recipeRepo->updateCoverImage($recipe, $request->file('image', null));

        if ($recipemenu) {
            $recipemenu->appendRecipe($recipe);
            Activity::addForEntity($recipemenu, ActivityType::RECIPEMENU_UPDATE);
        }

        // Redirect to draft edit screen if signed in
        if ($this->isSignedIn()) {
            return redirect($recipe->getUrlContent());
        }
        // Otherwise show the edit view if they're a guest
        $this->setPageTitle(trans('entities.pages_new'));
        return view('recipes.content', ['parent' => $recipe->slug]);
    }

    /**
     * Show form to continue editing a draft page.
     *
     * @throws NotFoundException
     */
    public function editDraft(string $recipeSlug)
    {
        $draft = $this->recipeRepo->getBySlug($recipeSlug);
        $this->checkOwnablePermission('page-create', $draft);
        $this->setPageTitle(trans('entities.pages_edit_draft'));

        $draftsEnabled = $this->isSignedIn();
        $templates = $this->recipeRepo->getTemplates(10);

        return view('pages.edit', [
            'page' => $draft,
            'recipe' => $draft,
            'isDraft' => true,
            'draftsEnabled' => $draftsEnabled,
            'templates' => $templates,
        ]);
    }

    /**
     * Store a new page by changing a draft into a page.
     *
     * @throws NotFoundException
     * @throws ValidationException
     */
    public function storeContent(Request $request, string $recipeSlug)
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'max:255'],
        ]);
        $draftPage = $this->recipeRepo->getBySlug($recipeSlug);
        $this->checkOwnablePermission('page-create', $draftPage);

        $page = $this->recipeRepo->publishDraft($draftPage, $request->all());

        return redirect($page->getUrlContent());
    }

    /**
     * Display the specified page.
     * If the page is not found via the slug the revisions are searched for a match.
     *
     * @throws NotFoundException
     */
    public function showContent(string $recipeSlug)
    {
        try {
            $page = $this->recipeRepo->getBySlug($recipeSlug);
        } catch (NotFoundException $e) {
            $page = $this->recipeRepo->getByOldSlug($recipeSlug);

            if ($page === null) {
                throw $e;
            }

            return redirect($page->getUrlContent());
        }

        $this->checkOwnablePermission('page-view', $page);

        $pageContent = (new PageContent($page));
        $page->html = $pageContent->render();

        $pageNav = $pageContent->getNavigation($page->html);

        // Check if page comments are enabled
        $commentsEnabled = !setting('app-disable-comments');
        if ($commentsEnabled) {
            $page->load(['comments.createdBy']);
        }

        $nextPreviousLocator = new NextPreviousContentLocator($page);

        View::incrementFor($page);
        $this->setPageTitle($page->getShortName());

        return view('pages.show', [
            'page' => $page,
            'recipe' => $page,
            'current' => $page,
            'commentsEnabled' => $commentsEnabled,
            'pageNav' => $pageNav,
            'next' => $nextPreviousLocator->getNext(),
            'previous' => $nextPreviousLocator->getPrevious(),
        ]);
    }

    /**
     * Show the form for editing the specified page.
     *
     * @throws NotFoundException
     */
    public function editContent(string $recipeSlug)
    {
        $page = $this->recipeRepo->getBySlug($recipeSlug);
        $this->checkOwnablePermission('page-update', $page);

        $page->isDraft = false;
        $editActivity = new PageEditActivity($page);

        // Check for active editing
        $warnings = [];
        if ($editActivity->hasActiveEditing()) {
            $warnings[] = $editActivity->activeEditingMessage();
        }

        // Check for a current draft version for this user
        $userDraft = $this->recipeRepo->getUserDraft($page);
        if ($userDraft !== null) {
            $page->forceFill($userDraft->only(['name', 'html', 'markdown']));
            $page->isDraft = true;
            $warnings[] = $editActivity->getEditingActiveDraftMessage($userDraft);
        }

        if (count($warnings) > 0) {
            $this->showWarningNotification(implode("\n", $warnings));
        }

        $templates = $this->recipeRepo->getTemplates(10);
        $draftsEnabled = $this->isSignedIn();
        $this->setPageTitle(trans('entities.pages_editing_named', ['pageName' => $page->getShortName()]));

        return view('pages.edit', [
            'page' => $page,
            'recipe' => $page,
            'current' => $page,
            'draftsEnabled' => $draftsEnabled,
            'templates' => $templates,
        ]);


    }

    /**
     * Update the specified page in storage.
     *
     * @throws ValidationException
     * @throws NotFoundException
     */
    public function updateContent(Request $request, string $recipeSlug)
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'max:255'],
        ]);
        $page = $this->recipeRepo->getBySlug($recipeSlug);
        $this->checkOwnablePermission('page-update', $page);

        $this->recipeRepo->update($page, $request->all());


        return redirect($page->getUrl());
    }

    /**
     * Create a new page as a guest user.
     *
     * @throws ValidationException
     */
    public function createAsGuest(Request $request, string $recipeSlug)
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'max:255'],
        ]);

        $parent = $this->recipeRepo->getBySlug($recipeSlug);
        $this->checkOwnablePermission('page-create', $parent);

        $recipe = $this->recipeRepo->create($request->all());
        $this->recipeRepo->publishDraft($recipe, [
            'name' => $request->get('name'),
            'html' => '',
        ]);

        return redirect($recipe->getUrlContent('/edit'));
    }
}
