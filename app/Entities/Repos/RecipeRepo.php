<?php

namespace DailyRecipe\Entities\Repos;

use DailyRecipe\Actions\ActivityType;
use DailyRecipe\Actions\TagRepo;

use DailyRecipe\Entities\Models\RecipeRevision;
use DailyRecipe\Entities\Models\Recipe;
use DailyRecipe\Entities\Tools\RecipeContents;
use DailyRecipe\Entities\Tools\TrashCan;
use DailyRecipe\Exceptions\ImageUploadException;
use DailyRecipe\Exceptions\NotFoundException;
use DailyRecipe\Facades\Activity;
use DailyRecipe\Uploads\ImageRepo;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
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
     * Get a recipe by its slug.
     */
    public function getById(string $recipeId): Recipe
    {
        $recipe = Recipe::visible()->where('id', '=', $recipeId)->first();

        if ($recipe === null) {
            throw new NotFoundException(trans('errors.recipe_not_found'));
        }

        return $recipe;
    }

    /**
     * Get a page by its old slug but checking the revisions table
     * for the last revision that matched the given page and recipe slug.
     */
    public function getByOldSlug(string $recipeSlug): ?Recipe
    {
        /** @var ?RecipeRevision $revision */
        $revision = RecipeRevision::query()
            ->whereHas('page', function (Builder $query) {
                $query->scopes('visible');
            })
            ->where('slug', '=', $recipeSlug)
            ->where('type', '=', 'version')
            ->orderBy('created_at', 'desc')
            ->with('page')
            ->first();

        return $revision->recipe ?? null;
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


    /**
     * Get pages that have been marked as a template.
     */
    public function getTemplates(int $count = 10, int $page = 1, string $search = ''): \Illuminate\Pagination\LengthAwarePaginator
    {
        $query = Recipe::visible()
            ->where('template', '=', true)
            ->orderBy('name', 'asc')
            ->skip(($page - 1) * $count)
            ->take($count);

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        $paginator = $query->paginate($count, ['*'], 'page', $page);
        $paginator->withPath('/templates');

        return $paginator;
    }

    /**
     * Publish a draft page to make it a live, non-draft page.
     */
    public function publishDraft(Recipe $draft, array $input): Recipe
    {
        $this->updateTemplateStatusAndContentFromInput($draft, $input);
        $this->baseRepo->update($draft, $input);

        $draft->draft = false;
        $draft->revision_count = 1;
        $draft->priority = $this->getNewPriority($draft);
        $draft->refreshSlug();
        $draft->save();

        $this->savePageRevision($draft, trans('entities.pages_initial_revision'));
        $draft->indexForSearch();
        $draft->refresh();

        Activity::addForEntity($draft, ActivityType::PAGE_CREATE);

        return $draft;
    }

    public function publishDraftNew(Recipe $draft): Recipe
    {

        $draft->draft = false;

        $draft->refreshSlug();
        $draft->save();

        $draft->indexForSearch();
        $draft->refresh();

        Activity::addForEntity($draft, ActivityType::PAGE_CREATE);

        return $draft;
    }

    protected function updateTemplateStatusAndContentFromInput(Recipe $page, array $input)
    {
        if (isset($input['template']) && userCan('templates-manage')) {
            $page->template = ($input['template'] === 'true');
        }

        $pageContent = new RecipeContents($page);
        if (!empty($input['markdown'] ?? '')) {
            $pageContent->setNewMarkdown($input['markdown']);
        } elseif (isset($input['html'])) {
            $pageContent->setNewHTML($input['html']);
        }
    }

    /**
     * Get a new priority for a page.
     */
    protected function getNewPriority(Recipe $page): int
    {
        return (new RecipeContents($page))->getLastPriority() + 1;
    }

    /**
     * Saves a page revision into the system.
     */
    protected function savePageRevision(Recipe $page, string $summary = null): RecipeRevision
    {
        $revision = new RecipeRevision($page->getAttributes());

        $revision->recipe_id = $page->id;
        $revision->slug = $page->slug;
        $revision->created_by = user()->id;
        $revision->created_at = $page->updated_at;
        $revision->type = 'version';
        $revision->summary = $summary;
        $revision->revision_number = $page->revision_count;
        $revision->save();

        $this->deleteOldRevisions($page);

        return $revision;
    }

    /**
     * Delete old revisions, for the given page, from the system.
     */
    protected function deleteOldRevisions(Recipe $page)
    {
        $revisionLimit = config('app.revision_limit');
        if ($revisionLimit === false) {
            return;
        }

        $revisionsToDelete = RecipeRevision::query()
            ->where('recipe_id', '=', $page->id)
            ->orderBy('created_at', 'desc')
            ->skip(intval($revisionLimit))
            ->take(10)
            ->get(['id']);
        if ($revisionsToDelete->count() > 0) {
            RecipeRevision::query()->whereIn('id', $revisionsToDelete->pluck('id'))->delete();
        }
    }

    /**
     * Get the draft copy of the given page for the current user.
     */
    public function getUserDraft(Recipe $page): ?RecipeRevision
    {
        $revision = $this->getUserDraftQuery($page)->first();

        return $revision;
    }

    /**
     * Get the query to find the user's draft copies of the given page.
     */
    protected function getUserDraftQuery(Recipe $page)
    {
        return RecipeRevision::query()->where('created_by', '=', user()->id)
            ->where('type', 'update_draft')
            ->where('recipe_id', '=', $page->id)
            ->orderBy('created_at', 'desc');
    }

    /**
     * Save a page update draft.
     */
    public function updatePageDraft(Recipe $page, array $input)
    {
        // If the page itself is a draft simply update that
        if ($page->draft) {
            $this->updateTemplateStatusAndContentFromInput($page, $input);
            $page->fill($input);
            $page->save();

            return $page;
        }

        // Otherwise save the data to a revision
        $draft = $this->getPageRevisionToUpdate($page);
        $draft->fill($input);
        if (setting('app-editor') !== 'markdown') {
            $draft->markdown = '';
        }

        $draft->save();

        return $draft;
    }

    /**
     * Get a page revision to update for the given page.
     * Checks for an existing revisions before providing a fresh one.
     */
    protected function getPageRevisionToUpdate(Recipe $page): RecipeRevision
    {
        $drafts = $this->getUserDraftQuery($page)->get();
        if ($drafts->count() > 0) {
            return $drafts->first();
        }

        $draft = new RecipeRevision();
        $draft->slug = $page->slug;
        $draft->created_by = user()->id;
        $draft->type = 'update_draft';

        return $draft;
    }

    /**
     * Update a page in the system.
     */
    public function updateContent(Recipe $page, array $input): Recipe
    {
        // Hold the old details to compare later
        $oldHtml = $page->html;
        $oldName = $page->name;
        $oldMarkdown = $page->markdown;

        $this->updateTemplateStatusAndContentFromInput($page, $input);
        $this->baseRepo->update($page, $input);

        // Update with new details
        $page->revision_count++;
        $page->save();

        // Remove all update drafts for this user & page.
        $this->getUserDraftQuery($page)->delete();

        // Save a revision after updating
        $summary = trim($input['summary'] ?? '');
        $htmlChanged = isset($input['html']) && $input['html'] !== $oldHtml;
        $nameChanged = isset($input['name']) && $input['name'] !== $oldName;
        $markdownChanged = isset($input['markdown']) && $input['markdown'] !== $oldMarkdown;
        if ($htmlChanged || $nameChanged || $markdownChanged || $summary) {
            $this->savePageRevision($page, $summary);
        }

        Activity::addForEntity($page, ActivityType::PAGE_UPDATE);

        return $page;
    }

    /**
     * Restores a revision's content back into a page.
     */
    public function restoreRevision(Recipe $page, int $revisionId): Recipe
    {
        $page->revision_count++;

        /** @var RecipeRevision $revision */
        $revision = $page->revisions()->where('id', '=', $revisionId)->first();

        $page->fill($revision->toArray());
        $content = new RecipeContents($page);

        if (!empty($revision->markdown)) {
            $content->setNewMarkdown($revision->markdown);
        } else {
            $content->setNewHTML($revision->html);
        }

        $page->updated_by = user()->id;
        $page->refreshSlug();
        $page->save();
        $page->indexForSearch();

        $summary = trans('entities.pages_revision_restored_from', ['id' => strval($revisionId), 'summary' => $revision->summary]);
        $this->savePageRevision($page, $summary);

        Activity::addForEntity($page, ActivityType::PAGE_RESTORE);

        return $page;
    }

}
