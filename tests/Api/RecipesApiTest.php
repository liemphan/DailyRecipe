<?php

namespace Tests\Api;

use DailyRecipe\Entities\Models\Recipe;
use Tests\TestCase;

class RecipesApiTest extends TestCase
{
    use TestsApi;

    protected $baseEndpoint = '/api/recipes';

    public function test_index_endpoint_returns_expected_recipe()
    {
        $this->actingAsApiEditor();
        $firstRecipe = Recipe::query()->orderBy('id', 'asc')->first();

        $resp = $this->getJson($this->baseEndpoint . '?count=1&sort=+id');
        $resp->assertJson(['data' => [
            [
                'id' => $firstRecipe->id,
                'name' => $firstRecipe->name,
                'slug' => $firstRecipe->slug,
            ],
        ]]);
    }

    public function test_create_endpoint()
    {
        $this->actingAsApiEditor();
        $details = [
            'name' => 'My API recipe',
            'description' => 'A recipe created via the API',
        ];

        $resp = $this->postJson($this->baseEndpoint, $details);
        $resp->assertStatus(200);
        $newItem = Recipe::query()->orderByDesc('id')->where('name', '=', $details['name'])->first();
        $resp->assertJson(array_merge($details, ['id' => $newItem->id, 'slug' => $newItem->slug]));
        $this->assertActivityExists('recipe_create', $newItem);
    }

    public function test_recipe_name_needed_to_create()
    {
        $this->actingAsApiEditor();
        $details = [
            'description' => 'A recipe created via the API',
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
        $recipe = Recipe::visible()->first();

        $resp = $this->getJson($this->baseEndpoint . "/{$recipe->id}");

        $resp->assertStatus(200);
        $resp->assertJson([
            'id' => $recipe->id,
            'slug' => $recipe->slug,
            'created_by' => [
                'name' => $recipe->createdBy->name,
            ],
            'updated_by' => [
                'name' => $recipe->createdBy->name,
            ],
            'owned_by' => [
                'name' => $recipe->ownedBy->name,
            ],
        ]);
    }

    public function test_update_endpoint()
    {
        $this->actingAsApiEditor();
        $recipe = Recipe::visible()->first();
        $details = [
            'name' => 'My updated API recipe',
            'description' => 'A recipe created via the API',
        ];

        $resp = $this->putJson($this->baseEndpoint . "/{$recipe->id}", $details);
        $recipe->refresh();

        $resp->assertStatus(200);
        $resp->assertJson(array_merge($details, ['id' => $recipe->id, 'slug' => $recipe->slug]));
        $this->assertActivityExists('recipe_update', $recipe);
    }

    public function test_delete_endpoint()
    {
        $this->actingAsApiEditor();
        $recipe = Recipe::visible()->first();
        $resp = $this->deleteJson($this->baseEndpoint . "/{$recipe->id}");

        $resp->assertStatus(204);
        $this->assertActivityExists('recipe_delete');
    }

    public function test_export_html_endpoint()
    {
        $this->actingAsApiEditor();
        $recipe = Recipe::visible()->first();

        $resp = $this->get($this->baseEndpoint . "/{$recipe->id}/export/html");
        $resp->assertStatus(200);
        $resp->assertSee($recipe->name);
        $resp->assertHeader('Content-Disposition', 'attachment; filename="' . $recipe->slug . '.html"');
    }

    public function test_export_plain_text_endpoint()
    {
        $this->actingAsApiEditor();
        $recipe = Recipe::visible()->first();

        $resp = $this->get($this->baseEndpoint . "/{$recipe->id}/export/plaintext");
        $resp->assertStatus(200);
        $resp->assertSee($recipe->name);
        $resp->assertHeader('Content-Disposition', 'attachment; filename="' . $recipe->slug . '.txt"');
    }

    public function test_export_pdf_endpoint()
    {
        $this->actingAsApiEditor();
        $recipe = Recipe::visible()->first();

        $resp = $this->get($this->baseEndpoint . "/{$recipe->id}/export/pdf");
        $resp->assertStatus(200);
        $resp->assertHeader('Content-Disposition', 'attachment; filename="' . $recipe->slug . '.pdf"');
    }

    public function test_export_markdown_endpoint()
    {
        $this->actingAsApiEditor();
        $recipe = Recipe::visible()->has('pages')->has('chapters')->first();

        $resp = $this->get($this->baseEndpoint . "/{$recipe->id}/export/markdown");
        $resp->assertStatus(200);
        $resp->assertHeader('Content-Disposition', 'attachment; filename="' . $recipe->slug . '.md"');
        $resp->assertSee('# ' . $recipe->name);
        $resp->assertSee('# ' . $recipe->pages()->first()->name);
        $resp->assertSee('# ' . $recipe->chapters()->first()->name);
    }

    public function test_cant_export_when_not_have_permission()
    {
        $types = ['html', 'plaintext', 'pdf', 'markdown'];
        $this->actingAsApiEditor();
        $this->removePermissionFromUser($this->getEditor(), 'content-export');

        $recipe = Recipe::visible()->first();
        foreach ($types as $type) {
            $resp = $this->get($this->baseEndpoint . "/{$recipe->id}/export/{$type}");
            $this->assertPermissionError($resp);
        }
    }
}
