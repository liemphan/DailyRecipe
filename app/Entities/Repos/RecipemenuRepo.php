<?php

namespace DailyRecipe\Entities\Repos;

use DailyRecipe\Actions\ActivityType;
use DailyRecipe\Entities\Models\Book;
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
            ->with(['visibleBooks', 'cover'])
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
    public function create(array $input, array $bookIds): Recipemenu
    {
        $menu = new Recipemenu();
        $this->baseRepo->create($menu, $input);
        $this->updateBooks($menu, $bookIds);
        Activity::addForEntity($menu, ActivityType::RECIPEMENU_CREATE);

        return $menu;
    }

    /**
     * Update an existing menu in the system using the given input.
     */
    public function update(Recipemenu $menu, array $input, ?array $bookIds): Recipemenu
    {
        $this->baseRepo->update($menu, $input);

        if (!is_null($bookIds)) {
            $this->updateBooks($menu, $bookIds);
        }

        Activity::addForEntity($menu, ActivityType::RECIPEMENU_UPDATE);

        return $menu;
    }

    /**
     * Update which books are assigned to this menu by
     * syncing the given book ids.
     * Function ensures the books are visible to the current user and existing.
     */
    protected function updateBooks(Recipemenu $menu, array $bookIds)
    {
        $numericIDs = collect($bookIds)->map(function ($id) {
            return intval($id);
        });

        $syncData = Book::visible()
            ->whereIn('id', $bookIds)
            ->pluck('id')
            ->mapWithKeys(function ($bookId) use ($numericIDs) {
                return [$bookId => ['order' => $numericIDs->search($bookId)]];
            });

        $menu->books()->sync($syncData);
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
     * Copy down the permissions of the given menu to all child books.
     */
    public function copyDownPermissions(Recipemenu $menu, $checkUserPermissions = true): int
    {
        $menuPermissions = $menu->permissions()->get(['role_id', 'action'])->toArray();
        $menuBooks = $menu->books()->get(['id', 'restricted']);
        $updatedBookCount = 0;

        /** @var Book $book */
        foreach ($menuBooks as $book) {
            if ($checkUserPermissions && !userCan('restrictions-manage', $book)) {
                continue;
            }
            $book->permissions()->delete();
            $book->restricted = $menu->restricted;
            $book->permissions()->createMany($menuPermissions);
            $book->save();
            $book->rebuildPermissions();
            $updatedBookCount++;
        }

        return $updatedBookCount;
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
