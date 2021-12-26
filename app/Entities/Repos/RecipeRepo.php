<?php

namespace DailyRecipe\Entities\Repos;

use DailyRecipe\Actions\ActivityType;
use DailyRecipe\Actions\TagRepo;
use DailyRecipe\Entities\Models\Recipe;
use DailyRecipe\Entities\Tools\TrashCan;
use DailyRecipe\Exceptions\ImageUploadException;
use DailyRecipe\Exceptions\NotFoundException;
use DailyRecipe\Facades\Activity;
use DailyRecipe\Uploads\ImageRepo;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

class RecipeRepo
{
    protected $baseRepo;
    protected $tagRepo;
    protected $imageRepo;

    /**
     * RecipeRepo constructor.
     */
    public function __construct(BaseRepo $baseRepo, TagRepo $tagRepo, ImageRepo $imageRepo)
    {
        $this->baseRepo = $baseRepo;
        $this->tagRepo = $tagRepo;
        $this->imageRepo = $imageRepo;
    }

    /**
     * Get all recipes in a paginated format.
     */
    public function getAllPaginated(int $count = 20, string $sort = 'name', string $order = 'asc'): LengthAwarePaginator
    {
        return Recipe::visible()->with('cover')->orderBy($sort, $order)->paginate($count);
    }

    /**
     * Get the recipes that were most recently viewed by this user.
     */
    public function getRecentlyViewed(int $count = 20): Collection
    {
        return Recipe::visible()->withLastView()
            ->having('last_viewed_at', '>', 0)
            ->orderBy('last_viewed_at', 'desc')
            ->take($count)->get();
    }

    /**
     * Get the most popular recipes in the system.
     */
    public function getPopular(int $count = 20): Collection
    {
        return Recipe::visible()->withViewCount()
            ->having('view_count', '>', 0)
            ->orderBy('view_count', 'desc')
            ->take($count)->get();
    }

    /**
     * Get the most recently created recipes from the system.
     */
    public function getRecentlyCreated(int $count = 20): Collection
    {
        return Recipe::visible()->orderBy('created_at', 'desc')
            ->take($count)->get();
    }

    /**
     * Get a recipe by its slug.
     */
    public function getBySlug(string $slug): Recipe
    {
        $recipe = Recipe::visible()->where('slug', '=', $slug)->first();

        if ($recipe === null) {
            throw new NotFoundException(trans('errors.recipe_not_found'));
        }

        return $recipe;
    }

    /**
     * Create a new recipe in the system.
     */
    public function create(array $input): Recipe
    {
        $recipe = new Recipe();
        $this->baseRepo->create($recipe, $input);
        Activity::addForEntity($recipe, ActivityType::RECIPE_CREATE);

        return $recipe;
    }

    /**
     * Update the given recipe.
     */
    public function update(Recipe $recipe, array $input): Recipe
    {
        $this->baseRepo->update($recipe, $input);
        Activity::addForEntity($recipe, ActivityType::RECIPE_UPDATE);

        return $recipe;
    }

    /**
     * Update the given recipe's cover image, or clear it.
     *
     * @throws ImageUploadException
     * @throws Exception
     */
    public function updateCoverImage(Recipe $recipe, ?UploadedFile $coverImage, bool $removeImage = false)
    {
        $this->baseRepo->updateCoverImage($recipe, $coverImage, $removeImage);
    }

    /**
     * Remove a recipe from the system.
     *
     * @throws Exception
     */
    public function destroy(Recipe $recipe)
    {
        $trashCan = new TrashCan();
        $trashCan->softDestroyRecipe($recipe);
        Activity::addForEntity($recipe, ActivityType::RECIPE_DELETE);

        $trashCan->autoClearOld();
    }
}
