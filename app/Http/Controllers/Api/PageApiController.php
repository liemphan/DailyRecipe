<?php

namespace DailyRecipe\Http\Controllers\Api;

use DailyRecipe\Entities\Models\Recipe;
use DailyRecipe\Entities\Models\Chapter;
use DailyRecipe\Entities\Models\Page;
use DailyRecipe\Entities\Repos\PageRepo;
use DailyRecipe\Exceptions\PermissionsException;
use Exception;
use Illuminate\Http\Request;

class PageApiController extends ApiController
{
    protected $pageRepo;

    protected $rules = [
        'create' => [
            'recipe_id'    => ['required_without:chapter_id', 'integer'],
            'chapter_id' => ['required_without:recipe_id', 'integer'],
            'name'       => ['required', 'string', 'max:255'],
            'html'       => ['required_without:markdown', 'string'],
            'markdown'   => ['required_without:html', 'string'],
            'tags'       => ['array'],
        ],
        'update' => [
            'recipe_id'    => ['required', 'integer'],
            'chapter_id' => ['required', 'integer'],
            'name'       => ['string', 'min:1', 'max:255'],
            'html'       => ['string'],
            'markdown'   => ['string'],
            'tags'       => ['array'],
        ],
    ];

    public function __construct(PageRepo $pageRepo)
    {
        $this->pageRepo = $pageRepo;
    }

    /**
     * Get a listing of pages visible to the user.
     */
    public function list()
    {
        $pages = Page::visible();

        return $this->apiListingResponse($pages, [
            'id', 'recipe_id', 'chapter_id', 'name', 'slug', 'priority',
            'draft', 'template',
            'created_at', 'updated_at',
            'created_by', 'updated_by', 'owned_by',
        ]);
    }

    /**
     * Create a new page in the system.
     *
     * The ID of a parent recipe or chapter is required to indicate
     * where this page should be located.
     *
     * Any HTML content provided should be kept to a single-block depth of plain HTML
     * elements to remain compatible with the DailyRecipe front-end and editors.
     * Any images included via base64 data URIs will be extracted and saved as gallery
     * images against the page during upload.
     */
    public function create(Request $request)
    {
        $this->validate($request, $this->rules['create']);

        if ($request->has('chapter_id')) {
            $parent = Chapter::visible()->findOrFail($request->get('chapter_id'));
        } else {
            $parent = Recipe::visible()->findOrFail($request->get('recipe_id'));
        }
        $this->checkOwnablePermission('page-create', $parent);

        $draft = $this->pageRepo->getNewDraftPage($parent);
        $this->pageRepo->publishDraft($draft, $request->only(array_keys($this->rules['create'])));

        return response()->json($draft->forJsonDisplay());
    }

    /**
     * View the details of a single page.
     *
     * Pages will always have HTML content. They may have markdown content
     * if the markdown editor was used to last update the page.
     */
    public function read(string $id)
    {
        $page = $this->pageRepo->getById($id, []);

        return response()->json($page->forJsonDisplay());
    }

    /**
     * Update the details of a single page.
     *
     * See the 'create' action for details on the provided HTML/Markdown.
     * Providing a 'recipe_id' or 'chapter_id' property will essentially move
     * the page into that parent element if you have permissions to do so.
     */
    public function update(Request $request, string $id)
    {
        $page = $this->pageRepo->getById($id, []);
        $this->checkOwnablePermission('page-update', $page);

        $parent = null;
        if ($request->has('chapter_id')) {
            $parent = Chapter::visible()->findOrFail($request->get('chapter_id'));
        } elseif ($request->has('recipe_id')) {
            $parent = Recipe::visible()->findOrFail($request->get('recipe_id'));
        }

        if ($parent && !$parent->matches($page->getParent())) {
            $this->checkOwnablePermission('page-delete', $page);

            try {
                $this->pageRepo->move($page, $parent->getType() . ':' . $parent->id);
            } catch (Exception $exception) {
                if ($exception instanceof  PermissionsException) {
                    $this->showPermissionError();
                }

                return $this->jsonError(trans('errors.selected_recipe_chapter_not_found'));
            }
        }

        $updatedPage = $this->pageRepo->update($page, $request->all());

        return response()->json($updatedPage->forJsonDisplay());
    }

    /**
     * Delete a page.
     * This will typically send the page to the recycle bin.
     */
    public function delete(string $id)
    {
        $page = $this->pageRepo->getById($id, []);
        $this->checkOwnablePermission('page-delete', $page);

        $this->pageRepo->destroy($page);

        return response('', 204);
    }
}
