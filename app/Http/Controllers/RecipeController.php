<?php

namespace DailyRecipe\Http\Controllers;

use Activity;
use DailyRecipe\Actions\ActivityType;
use DailyRecipe\Actions\View;
use DailyRecipe\Entities\Models\Recipemenu;
use DailyRecipe\Entities\Repos\RecipeRepo;
use DailyRecipe\Entities\Tools\RecipeContents;
use DailyRecipe\Entities\Tools\PermissionsUpdater;
use DailyRecipe\Entities\Tools\MenuContext;
use DailyRecipe\Exceptions\ImageUploadException;
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
            'recipes'   => $recipes,
            'recents' => $recents,
            'popular' => $popular,
            'new'     => $new,
            'view'    => $view,
            'sort'    => $sort,
            'order'   => $order,
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
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['string', 'max:1000'],
            'image'       => array_merge(['nullable'], $this->getImageValidationRules()),
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

        return redirect($recipe->getUrl());
    }

    /**
     * Display the specified recipe.
     */
    public function show(Request $request, string $slug)
    {
        $recipe = $this->recipeRepo->getBySlug($slug);
        $recipeChildren = (new RecipeContents($recipe))->getTree(true);
        $recipeParentMenus = $recipe->menus()->scopes('visible')->get();

        View::incrementFor($recipe);
        if ($request->has('menu')) {
            $this->entityContextManager->setMenuContext(intval($request->get('menu')));
        }

        $this->setPageTitle($recipe->getShortName());

        return view('recipes.show', [
            'recipe'              => $recipe,
            'current'           => $recipe,
            'recipeChildren'      => $recipeChildren,
            'recipeParentMenus' => $recipeParentMenus,
            'activity'          => Activity::entityActivity($recipe, 20, 1),
        ]);
    }

    /**
     * Show the form for editing the specified recipe.
     */
    public function edit(string $slug)
    {
        $recipe = $this->recipeRepo->getBySlug($slug);
        $this->checkOwnablePermission('recipe-update', $recipe);
        $this->setPageTitle(trans('entities.recipes_edit_named', ['recipeName'=>$recipe->getShortName()]));

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
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['string', 'max:1000'],
            'image'       => array_merge(['nullable'], $this->getImageValidationRules()),
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
}
