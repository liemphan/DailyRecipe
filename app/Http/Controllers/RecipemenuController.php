<?php

namespace DailyRecipe\Http\Controllers;

use Activity;
use DailyRecipe\Actions\View;
use DailyRecipe\Entities\Models\Recipe;
use DailyRecipe\Entities\Repos\RecipemenuRepo;
use DailyRecipe\Entities\Tools\PermissionsUpdater;
use DailyRecipe\Entities\Tools\MenuContext;
use DailyRecipe\Exceptions\ImageUploadException;
use DailyRecipe\Exceptions\NotFoundException;
use DailyRecipe\Uploads\ImageRepo;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class RecipemenuController extends Controller
{
    protected $recipemenuRepo;
    protected $entityContextManager;
    protected $imageRepo;

    public function __construct(RecipemenuRepo $recipemenuRepo, MenuContext $entityContextManager, ImageRepo $imageRepo)
    {
        $this->recipemenuRepo = $recipemenuRepo;
        $this->entityContextManager = $entityContextManager;
        $this->imageRepo = $imageRepo;
    }

    /**
     * Display a listing of the book.
     */
    public function index()
    {
        $view = setting()->getForCurrentUser('recipemenus_view_type');
        $sort = setting()->getForCurrentUser('recipemenus_sort', 'name');
        $order = setting()->getForCurrentUser('recipemenus_sort_order', 'asc');
        $sortOptions = [
            'name'       => trans('common.sort_name'),
            'created_at' => trans('common.sort_created_at'),
            'updated_at' => trans('common.sort_updated_at'),
        ];

        $menus = $this->recipemenuRepo->getAllPaginated(18, $sort, $order);
        $recents = $this->isSignedIn() ? $this->recipemenuRepo->getRecentlyViewed(4) : false;
        $popular = $this->recipemenuRepo->getPopular(4);
        $new = $this->recipemenuRepo->getRecentlyCreated(4);

        $this->entityContextManager->clearMenuContext();
        $this->setPageTitle(trans('entities.menus'));

        return view('menus.index', [
            'menus'     => $menus,
            'recents'     => $recents,
            'popular'     => $popular,
            'new'         => $new,
            'view'        => $view,
            'sort'        => $sort,
            'order'       => $order,
            'sortOptions' => $sortOptions,
        ]);
    }

    /**
     * Show the form for creating a new recipemenu.
     */
    public function create()
    {
        $this->checkPermission('recipemenu-create-all');
        $books = Recipe::hasPermission('update')->get();
        $this->setPageTitle(trans('entities.menus_create'));

        return view('menus.create', ['books' => $books]);
    }

    /**
     * Store a newly created recipemenu in storage.
     *
     * @throws ValidationException
     * @throws ImageUploadException
     */
    public function store(Request $request)
    {
        $this->checkPermission('recipemenu-create-all');
        $this->validate($request, [
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['string', 'max:1000'],
            'image'       => array_merge(['nullable'], $this->getImageValidationRules()),
        ]);

        $bookIds = explode(',', $request->get('books', ''));
        $menu = $this->recipemenuRepo->create($request->all(), $bookIds);
        $this->recipemenuRepo->updateCoverImage($menu, $request->file('image', null));

        return redirect($menu->getUrl());
    }

    /**
     * Display the recipemenu of the given slug.
     *
     * @throws NotFoundException
     */
    public function show(string $slug)
    {
        $menu = $this->recipemenuRepo->getBySlug($slug);
        $this->checkOwnablePermission('book-view', $menu);

        $sort = setting()->getForCurrentUser('menu_books_sort', 'default');
        $order = setting()->getForCurrentUser('menu_books_sort_order', 'asc');

        $sortedVisibleMenuBooks = $menu->visibleRecipes()->get()
            ->sortBy($sort === 'default' ? 'pivot.order' : $sort, SORT_REGULAR, $order === 'desc')
            ->values()
            ->all();

        View::incrementFor($menu);
        $this->entityContextManager->setMenuContext($menu->id);
        $view = setting()->getForCurrentUser('recipemenu_view_type');

        $this->setPageTitle($menu->getShortName());

        return view('menus.show', [
            'menu'                   => $menu,
            'sortedVisibleMenuBooks' => $sortedVisibleMenuBooks,
            'view'                    => $view,
            'activity'                => Activity::entityActivity($menu, 20, 1),
            'order'                   => $order,
            'sort'                    => $sort,
        ]);
    }

    /**
     * Show the form for editing the specified recipemenu.
     */
    public function edit(string $slug)
    {
        $menu = $this->recipemenuRepo->getBySlug($slug);
        $this->checkOwnablePermission('recipemenu-update', $menu);

        $menuBookIds = $menu->books()->get(['id'])->pluck('id');
        $books = Recipe::hasPermission('update')->whereNotIn('id', $menuBookIds)->get();

        $this->setPageTitle(trans('entities.menus_edit_named', ['name' => $menu->getShortName()]));

        return view('menus.edit', [
            'menu' => $menu,
            'books' => $books,
        ]);
    }

    /**
     * Update the specified recipemenu in storage.
     *
     * @throws ValidationException
     * @throws ImageUploadException
     * @throws NotFoundException
     */
    public function update(Request $request, string $slug)
    {
        $menu = $this->recipemenuRepo->getBySlug($slug);
        $this->checkOwnablePermission('recipemenu-update', $menu);
        $this->validate($request, [
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['string', 'max:1000'],
            'image'       => array_merge(['nullable'], $this->getImageValidationRules()),
        ]);

        $bookIds = explode(',', $request->get('books', ''));
        $menu = $this->recipemenuRepo->update($menu, $request->all(), $bookIds);
        $resetCover = $request->has('image_reset');
        $this->recipemenuRepo->updateCoverImage($menu, $request->file('image', null), $resetCover);

        return redirect($menu->getUrl());
    }

    /**
     * Shows the page to confirm deletion.
     */
    public function showDelete(string $slug)
    {
        $menu = $this->recipemenuRepo->getBySlug($slug);
        $this->checkOwnablePermission('recipemenu-delete', $menu);

        $this->setPageTitle(trans('entities.menus_delete_named', ['name' => $menu->getShortName()]));

        return view('menus.delete', ['menu' => $menu]);
    }

    /**
     * Remove the specified recipemenu from storage.
     *
     * @throws Exception
     */
    public function destroy(string $slug)
    {
        $menu = $this->recipemenuRepo->getBySlug($slug);
        $this->checkOwnablePermission('recipemenu-delete', $menu);

        $this->recipemenuRepo->destroy($menu);

        return redirect('/menus');
    }

    /**
     * Show the permissions view.
     */
    public function showPermissions(string $slug)
    {
        $menu = $this->recipemenuRepo->getBySlug($slug);
        $this->checkOwnablePermission('restrictions-manage', $menu);

        return view('menus.permissions', [
            'menu' => $menu,
        ]);
    }

    /**
     * Set the permissions for this recipemenu.
     */
    public function permissions(Request $request, PermissionsUpdater $permissionsUpdater, string $slug)
    {
        $menu = $this->recipemenuRepo->getBySlug($slug);
        $this->checkOwnablePermission('restrictions-manage', $menu);

        $permissionsUpdater->updateFromPermissionsForm($menu, $request);

        $this->showSuccessNotification(trans('entities.menus_permissions_updated'));

        return redirect($menu->getUrl());
    }

    /**
     * Copy the permissions of a recipemenu to the child books.
     */
    public function copyPermissions(string $slug)
    {
        $menu = $this->recipemenuRepo->getBySlug($slug);
        $this->checkOwnablePermission('restrictions-manage', $menu);

        $updateCount = $this->recipemenuRepo->copyDownPermissions($menu);
        $this->showSuccessNotification(trans('entities.menus_copy_permission_success', ['count' => $updateCount]));

        return redirect($menu->getUrl());
    }
}
