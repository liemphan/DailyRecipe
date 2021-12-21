<?php

namespace DailyRecipe\Entities\Repos;

use DailyRecipe\Actions\ActivityType;
use DailyRecipe\Entities\Models\Recipe;
use DailyRecipe\Entities\Models\Recipemenus;
use DailyRecipe\Entities\Tools\TrashCan;
use DailyRecipe\Exceptions\ImageUploadException;
use DailyRecipe\Exceptions\NotFoundException;
use DailyRecipe\Facades\Activity;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

class BookshelfRepo
{
    protected $baseRepo;

    /**
     * BookshelfRepo constructor.
     */
    public function __construct(BaseRepo $baseRepo)
    {
        $this->baseRepo = $baseRepo;
    }

    /**
     * Get all bookshelves in a paginated format.
     */
    public function getAllPaginated(int $count = 20, string $sort = 'name', string $order = 'asc'): LengthAwarePaginator
    {
        return Recipemenus::visible()
            ->with(['visibleBooks', 'cover'])
            ->orderBy($sort, $order)
            ->paginate($count);
    }

    /**
     * Get the bookshelves that were most recently viewed by this user.
     */
    public function getRecentlyViewed(int $count = 20): Collection
    {
        return Recipemenus::visible()->withLastView()
            ->having('last_viewed_at', '>', 0)
            ->orderBy('last_viewed_at', 'desc')
            ->take($count)->get();
    }

    /**
     * Get the most popular bookshelves in the system.
     */
    public function getPopular(int $count = 20): Collection
    {
        return Recipemenus::visible()->withViewCount()
            ->having('view_count', '>', 0)
            ->orderBy('view_count', 'desc')
            ->take($count)->get();
    }

    /**
     * Get the most recently created bookshelves from the system.
     */
    public function getRecentlyCreated(int $count = 20): Collection
    {
        return Recipemenus::visible()->orderBy('created_at', 'desc')
            ->take($count)->get();
    }

    /**
     * Get a shelf by its slug.
     */
    public function getBySlug(string $slug): Recipemenus
    {
        $shelf = Recipemenus::visible()->where('slug', '=', $slug)->first();

        if ($shelf === null) {
            throw new NotFoundException(trans('errors.bookshelf_not_found'));
        }

        return $shelf;
    }

    /**
     * Create a new shelf in the system.
     */
    public function create(array $input, array $bookIds): Recipemenus
    {
        $shelf = new Recipemenus();
        $this->baseRepo->create($shelf, $input);
        $this->updateBooks($shelf, $bookIds);
        Activity::addForEntity($shelf, ActivityType::BOOKSHELF_CREATE);

        return $shelf;
    }

    /**
     * Update an existing shelf in the system using the given input.
     */
    public function update(Recipemenus $shelf, array $input, ?array $bookIds): Recipemenus
    {
        $this->baseRepo->update($shelf, $input);

        if (!is_null($bookIds)) {
            $this->updateBooks($shelf, $bookIds);
        }

        Activity::addForEntity($shelf, ActivityType::BOOKSHELF_UPDATE);

        return $shelf;
    }

    /**
     * Update which recipes are assigned to this shelf by
     * syncing the given book ids.
     * Function ensures the recipes are visible to the current user and existing.
     */
    protected function updateBooks(Recipemenus $shelf, array $bookIds)
    {
        $numericIDs = collect($bookIds)->map(function ($id) {
            return intval($id);
        });

        $syncData = Recipe::visible()
            ->whereIn('id', $bookIds)
            ->pluck('id')
            ->mapWithKeys(function ($bookId) use ($numericIDs) {
                return [$bookId => ['order' => $numericIDs->search($bookId)]];
            });

        $shelf->books()->sync($syncData);
    }

    /**
     * Update the given shelf cover image, or clear it.
     *
     * @throws ImageUploadException
     * @throws Exception
     */
    public function updateCoverImage(Recipemenus $shelf, ?UploadedFile $coverImage, bool $removeImage = false)
    {
        $this->baseRepo->updateCoverImage($shelf, $coverImage, $removeImage);
    }

    /**
     * Copy down the permissions of the given shelf to all child recipes.
     */
    public function copyDownPermissions(Recipemenus $shelf, $checkUserPermissions = true): int
    {
        $shelfPermissions = $shelf->permissions()->get(['role_id', 'action'])->toArray();
        $shelfBooks = $shelf->books()->get(['id', 'restricted']);
        $updatedBookCount = 0;

        /** @var Recipe $book */
        foreach ($shelfBooks as $book) {
            if ($checkUserPermissions && !userCan('restrictions-manage', $book)) {
                continue;
            }
            $book->permissions()->delete();
            $book->restricted = $shelf->restricted;
            $book->permissions()->createMany($shelfPermissions);
            $book->save();
            $book->rebuildPermissions();
            $updatedBookCount++;
        }

        return $updatedBookCount;
    }

    /**
     * Remove a bookshelf from the system.
     *
     * @throws Exception
     */
    public function destroy(Recipemenus $shelf)
    {
        $trashCan = new TrashCan();
        $trashCan->softDestroyShelf($shelf);
        Activity::addForEntity($shelf, ActivityType::BOOKSHELF_DELETE);
        $trashCan->autoClearOld();
    }
}
