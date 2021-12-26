<?php

namespace DailyRecipe\Http\Controllers\Api;

use DailyRecipe\Entities\Models\Recipemenu;
use DailyRecipe\Entities\Repos\RecipemenuRepo;
use Exception;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class RecipemenuApiController extends ApiController
{
    /**
     * @var RecipemenuRepo
     */
    protected $recipemenuRepo;

    protected $rules = [
        'create' => [
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['string', 'max:1000'],
            'recipes'       => ['array'],
        ],
        'update' => [
            'name'        => ['string', 'min:1', 'max:255'],
            'description' => ['string', 'max:1000'],
            'recipes'       => ['array'],
        ],
    ];

    /**
     * RecipemenuApiController constructor.
     */
    public function __construct(RecipemenuRepo $recipemenuRepo)
    {
        $this->recipemenuRepo = $recipemenuRepo;
    }

    /**
     * Get a listing of menus visible to the user.
     */
    public function list()
    {
        $menus = Recipemenu::visible();

        return $this->apiListingResponse($menus, [
            'id', 'name', 'slug', 'description', 'created_at', 'updated_at', 'created_by', 'updated_by', 'owned_by', 'image_id',
        ]);
    }

    /**
     * Create a new menu in the system.
     * An array of recipes IDs can be provided in the request. These
     * will be added to the menu in the same order as provided.
     *
     * @throws ValidationException
     */
    public function create(Request $request)
    {
        $this->checkPermission('recipemenu-create-all');
        $requestData = $this->validate($request, $this->rules['create']);

        $recipeIds = $request->get('recipes', []);
        $menu = $this->recipemenuRepo->create($requestData, $recipeIds);

        return response()->json($menu);
    }

    /**
     * View the details of a single menu.
     */
    public function read(string $id)
    {
        $menu = Recipemenu::visible()->with([
            'tags', 'cover', 'createdBy', 'updatedBy', 'ownedBy',
            'recipes' => function (BelongsToMany $query) {
                $query->scopes('visible')->get(['id', 'name', 'slug']);
            },
        ])->findOrFail($id);

        return response()->json($menu);
    }

    /**
     * Update the details of a single menu.
     * An array of recipes IDs can be provided in the request. These
     * will be added to the menu in the same order as provided and overwrite
     * any existing recipe assignments.
     *
     * @throws ValidationException
     */
    public function update(Request $request, string $id)
    {
        $menu = Recipemenu::visible()->findOrFail($id);
        $this->checkOwnablePermission('recipemenu-update', $menu);

        $requestData = $this->validate($request, $this->rules['update']);
        $recipeIds = $request->get('recipes', null);

        $menu = $this->recipemenuRepo->update($menu, $requestData, $recipeIds);

        return response()->json($menu);
    }

    /**
     * Delete a single menu.
     * This will typically send the menu to the recycle bin.
     *
     * @throws Exception
     */
    public function delete(string $id)
    {
        $menu = Recipemenu::visible()->findOrFail($id);
        $this->checkOwnablePermission('recipemenu-delete', $menu);

        $this->recipemenuRepo->destroy($menu);

        return response('', 204);
    }
}
