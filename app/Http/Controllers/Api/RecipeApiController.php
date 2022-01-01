<?php

namespace DailyRecipe\Http\Controllers\Api;

use DailyRecipe\Entities\Models\Recipe;
use DailyRecipe\Entities\Repos\RecipeRepo;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class RecipeApiController extends ApiController
{
    protected $recipeRepo;

    protected $rules = [
        'create' => [
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['string', 'max:1000'],
            'tags'        => ['array'],
        ],
        'update' => [
            'name'        => ['string', 'min:1', 'max:255'],
            'description' => ['string', 'max:1000'],
            'tags'        => ['array'],
        ],
    ];

    public function __construct(RecipeRepo $recipeRepo)
    {
        $this->recipeRepo = $recipeRepo;
    }

    /**
     * Get a listing of recipes visible to the user.
     */
    public function list()
    {
        $recipes = Recipe::visible();

        return $this->apiListingResponse($recipes, [
            'id', 'name', 'slug', 'description', 'created_at', 'updated_at', 'created_by', 'updated_by', 'owned_by', 'image_id',
        ]);
    }

    /**
     * Create a new recipe in the system.
     *
     * @throws ValidationException
     */
    public function create(Request $request)
    {
        $this->checkPermission('recipe-create-all');
        $requestData = $this->validate($request, $this->rules['create']);

        $recipe = $this->recipeRepo->create($requestData);

        return response()->json($recipe);
    }

    /**
     * View the details of a single recipe.
     */
    public function read(string $id)
    {
        $recipe = Recipe::visible()->with(['tags', 'cover', 'createdBy', 'updatedBy', 'ownedBy'])->findOrFail($id);

        return response()->json($recipe);
    }

    /**
     * Update the details of a single recipe.
     *
     * @throws ValidationException
     */
    public function update(Request $request, string $id)
    {
        $recipe = Recipe::visible()->findOrFail($id);
        $this->checkOwnablePermission('recipe-update', $recipe);

        $requestData = $this->validate($request, $this->rules['update']);
        $recipe = $this->recipeRepo->update($recipe, $requestData);

        return response()->json($recipe);
    }

    /**
     * Delete a single recipe.
     * This will typically send the recipe to the recycle bin.
     *
     * @throws Exception
     */
    public function delete(string $id)
    {
        $recipe = Recipe::visible()->findOrFail($id);
        $this->checkOwnablePermission('recipe-delete', $recipe);

        $this->recipeRepo->destroy($recipe);

        return response('', 204);
    }
}
