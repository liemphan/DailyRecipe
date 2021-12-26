<?php

namespace DailyRecipe\Entities\Repos;

use DailyRecipe\Actions\ActivityType;
use DailyRecipe\Entities\Models\Recipe;
use DailyRecipe\Entities\Models\Recipemenu;
use DailyRecipe\Entities\Tools\TrashCan;
use DailyRecipe\Exceptions\ImageUploadException;
use DailyRecipe\Exceptions\NotFoundException;
use DailyRecipe\Facades\Activity;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

class RecipemenuRepo
{
    protected $baseRepo;

    /**
     * RecipemenuRepo constructor.
     */
    public function __construct(BaseRepo $baseRepo)
    {
        $this->baseRepo = $baseRepo;
    }

    /**
     * Get all recipemenus in a paginated format.
     */
    public function getAllPaginated(int $count = 20, string $sort = 'name', string $order = 'asc'): LengthAwarePaginator
    {
        return Recipemenu::visible()
            ->with(['visibleRecipes', 'cover'])
            ->orderBy($sort, $order)
            ->paginate($count);
    }

    /**
     * Get the recipemenus that were most recently viewed by this user.
     */
    public function getRecentlyViewed(int $count = 20): Collection
    {
        return Recipemenu::visible()->withLastView()
            ->having('last_viewed_at', '>', 0)
            ->orderBy('last_viewed_at', 'desc')
            ->take($count)->get();
    }

    /**
     * Get the most popular recipemenus in the system.
     */
    public function getPopular(int $count = 20): Collection
    {
        return Recipemenu::visible()->withViewCount()
            ->having('view_count', '>', 0)
            ->orderBy('view_count', 'desc')
            ->take($count)->get();
    }

    /**
     * Get the most recently created recipemenus from the system.
     */
    public function getRecentlyCreated(int $count = 20): Collection
    {
        return Recipemenu::visible()->orderBy('created_at', 'desc')
            ->take($count)->get();
    }

    /**
     * Get a menu by its slug.
     */
    public function getBySlug(string $slug): Recipemenu
    {
        $menu = Recipemenu::visible()->where('slug', '=', $slug)->first();

        if ($menu === null) {
            throw new NotFoundException(trans('errors.recipemenu_not_found'));
        }

        return $menu;
    }

    /**
     * Create a new menu in the system.
     */
    public function create(array $input, array $recipeIds): Recipemenu
    {
        $menu = new Recipemenu();
        $this->baseRepo->create($menu, $input);
        $this->updateRecipes($menu, $recipeIds);
        Activity::addForEntity($menu, ActivityType::RECIPEMENU_CREATE);

        return $menu;
    }

    /**
     * Update an existing menu in the system using the given input.
     */
    public function update(Recipemenu $menu, array $input, ?array $recipeIds): Recipemenu
    {
        $this->baseRepo->update($menu, $input);

        if (!is_null($recipeIds)) {
            $this->updateRecipes($menu, $recipeIds);
        }

        Activity::addForEntity($menu, ActivityType::RECIPEMENU_UPDATE);

        return $menu;
    }

    /**
     * Update which recipes are assigned to this menu by
     * syncing the given recipe ids.
     * Function ensures the recipes are visible to the current user and existing.
     */
    protected function updateRecipes(Recipemenu $menu, array $recipeIds)
    {
        $numericIDs = collect($recipeIds)->map(function ($id) {
            return intval($id);
        });

        $syncData = Recipe::visible()
            ->whereIn('id', $recipeIds)
            ->pluck('id')
            ->mapWithKeys(function ($recipeId) use ($numericIDs) {
                return [$recipeId => ['order' => $numericIDs->search($recipeId)]];
            });

        $menu->recipes()->sync($syncData);
    }

    /**
     * Update the given menu cover image, or clear it.
     *
     * @throws ImageUploadException
     * @throws Exception
     */
    public function updateCoverImage(Recipemenu $menu, ?UploadedFile $coverImage, bool $removeImage = false)
    {
        $this->baseRepo->updateCoverImage($menu, $coverImage, $removeImage);
    }

    /**
     * Copy down the permissions of the given menu to all child recipes.
     */
    public function copyDownPermissions(Recipemenu $menu, $checkUserPermissions = true): int
    {
        $menuPermissions = $menu->permissions()->get(['role_id', 'action'])->toArray();
        $menuRecipes = $menu->recipes()->get(['id', 'restricted']);
        $updatedRecipeCount = 0;

        /** @var Recipe $recipe */
        foreach ($menuRecipes as $recipe) {
            if ($checkUserPermissions && !userCan('restrictions-manage', $recipe)) {
                continue;
            }
            $recipe->permissions()->delete();
            $recipe->restricted = $menu->restricted;
            $recipe->permissions()->createMany($menuPermissions);
            $recipe->save();
            $recipe->rebuildPermissions();
            $updatedRecipeCount++;
        }

        return $updatedRecipeCount;
    }

    /**
     * Remove a recipemenu from the system.
     *
     * @throws Exception
     */
    public function destroy(Recipemenu $menu)
    {
        $trashCan = new TrashCan();
        $trashCan->softDestroyMenu($menu);
        Activity::addForEntity($menu, ActivityType::RECIPEMENU_DELETE);
        $trashCan->autoClearOld();
    }
}
