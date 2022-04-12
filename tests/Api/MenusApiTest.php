<?php

namespace Tests\Api;

use DailyRecipe\Entities\Models\Recipe;
use DailyRecipe\Entities\Models\RecipeMenu;
use Tests\TestCase;

class MenusApiTest extends TestCase
{
    use TestsApi;

    protected $baseEndpoint = '/api/menus';

    public function test_index_endpoint_returns_expected_menu()
    {
        $this->actingAsApiEditor();
        $firstRecipemenu = Recipemenu::query()->orderBy('id', 'asc')->first();

        $resp = $this->getJson($this->baseEndpoint . '?count=1&sort=+id');
        $resp->assertJson(['data' => [
            [
                'id' => $firstRecipemenu->id,
                'name' => $firstRecipemenu->name,
                'slug' => $firstRecipemenu->slug,
            ],
        ]]);
    }

    public function test_create_endpoint()
    {
        $this->actingAsApiEditor();
        $recipes = Recipe::query()->take(2)->get();

        $details = [
            'name' => 'My API menu',
            'description' => 'A menu created via the API',
        ];

        $resp = $this->postJson($this->baseEndpoint, array_merge($details, ['recipes' => [$recipes[0]->id, $recipes[1]->id]]));
        $resp->assertStatus(200);
        $newItem = Recipemenu::query()->orderByDesc('id')->where('name', '=', $details['name'])->first();
        $resp->assertJson(array_merge($details, ['id' => $newItem->id, 'slug' => $newItem->slug]));
        $this->assertActivityExists('menu_create', $newItem);
        foreach ($recipes as $index => $recipe) {
            $this->assertDatabaseHas('recipemenus_recipes', [
                'recipemenu_id' => $newItem->id,
                'recipe_id' => $recipe->id,
                'order' => $index,
            ]);
        }
    }

    public function test_menu_name_needed_to_create()
    {
        $this->actingAsApiEditor();
        $details = [
            'description' => 'A menu created via the API',
        ];

        $resp = $this->postJson($this->baseEndpoint, $details);
        $resp->assertStatus(422);
        $resp->assertJson([
            'error' => [
                'message' => 'The given data was invalid.',
                'validation' => [
                    'name' => ['The name field is required.'],
                ],
                'code' => 422,
            ],
        ]);
    }

    public function test_read_endpoint()
    {
        $this->actingAsApiEditor();
        $menu = Recipemenu::visible()->first();

        $resp = $this->getJson($this->baseEndpoint . "/{$menu->id}");

        $resp->assertStatus(200);
        $resp->assertJson([
            'id' => $menu->id,
            'slug' => $menu->slug,
            'created_by' => [
                'name' => $menu->createdBy->name,
            ],
            'updated_by' => [
                'name' => $menu->createdBy->name,
            ],
            'owned_by' => [
                'name' => $menu->ownedBy->name,
            ],
        ]);
    }

    public function test_update_endpoint()
    {
        $this->actingAsApiEditor();
        $menu = Recipemenu::visible()->first();
        $details = [
            'name' => 'My updated API menu',
            'description' => 'A menu created via the API',
        ];

        $resp = $this->putJson($this->baseEndpoint . "/{$menu->id}", $details);
        $menu->refresh();

        $resp->assertStatus(200);
        $resp->assertJson(array_merge($details, ['id' => $menu->id, 'slug' => $menu->slug]));
        $this->assertActivityExists('menu_update', $menu);
    }

    public function test_update_only_assigns_recipes_if_param_provided()
    {
        $this->actingAsApiEditor();
        $menu = Recipemenu::visible()->first();
        $this->assertTrue($menu->recipes()->count() > 0);
        $details = [
            'name' => 'My updated API menu',
        ];

        $resp = $this->putJson($this->baseEndpoint . "/{$menu->id}", $details);
        $resp->assertStatus(200);
        $this->assertTrue($menu->recipes()->count() > 0);

        $resp = $this->putJson($this->baseEndpoint . "/{$menu->id}", ['recipes' => []]);
        $resp->assertStatus(200);
        $this->assertTrue($menu->recipes()->count() === 0);
    }

    public function test_delete_endpoint()
    {
        $this->actingAsApiEditor();
        $menu = Recipemenu::visible()->first();
        $resp = $this->deleteJson($this->baseEndpoint . "/{$menu->id}");

        $resp->assertStatus(204);
        $this->assertActivityExists('menu_delete');
    }
}
