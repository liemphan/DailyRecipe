<?php

namespace Tests\Entity;

use DailyRecipe\Entities\Models\Recipe;
use Tests\TestCase;

class RecipeTest extends TestCase
{
    public function test_create()
    {
        $recipe = Recipe::factory()->make([
            'name' => 'My First Recipe',
        ]);

        $resp = $this->asEditor()->get('/recipes');
        $resp->assertElementContains('a[href="' . url('/create-recipe') . '"]', 'Create New Recipe');

        $resp = $this->get('/create-recipe');
        $resp->assertElementContains('form[action="' . url('/recipes') . '"][method="POST"]', 'Save Recipe');

        $resp = $this->post('/recipes', $recipe->only('name', 'description'));
        $resp->assertRedirect('/recipes/my-first-recipe');

        $resp = $this->get('/recipes/my-first-recipe');
        $resp->assertSee($recipe->name);
        $resp->assertSee($recipe->description);
    }

    public function test_create_uses_different_slugs_when_name_reused()
    {
        $recipe = Recipe::factory()->make([
            'name' => 'My First Recipe',
        ]);

        $this->asEditor()->post('/recipes', $recipe->only('name', 'description'));
        $this->asEditor()->post('/recipes', $recipe->only('name', 'description'));

        $recipes = Recipe::query()->where('name', '=', $recipe->name)
            ->orderBy('id', 'desc')
            ->take(2)
            ->get();

        $this->assertMatchesRegularExpression('/my-first-recipe-[0-9a-zA-Z]{3}/', $recipes[0]->slug);
        $this->assertEquals('my-first-recipe', $recipes[1]->slug);
    }

    public function test_update()
    {
        /** @var Recipe $recipe */
        $recipe = Recipe::query()->first();
        // Cheeky initial update to refresh slug
        $this->asEditor()->put($recipe->getUrl(), ['name' => $recipe->name . '5', 'description' => $recipe->description]);
        $recipe->refresh();

        $newName = $recipe->name . ' Updated';
        $newDesc = $recipe->description . ' with more content';

        $resp = $this->get($recipe->getUrl('/edit'));
        $resp->assertSee($recipe->name);
        $resp->assertSee($recipe->description);
        $resp->assertElementContains('form[action="' . $recipe->getUrl() . '"]', 'Save Recipe');

        $resp = $this->put($recipe->getUrl(), ['name' => $newName, 'description' => $newDesc]);
        $resp->assertRedirect($recipe->getUrl() . '-updated');

        $resp = $this->get($recipe->getUrl() . '-updated');
        $resp->assertSee($newName);
        $resp->assertSee($newDesc);
    }

    public function test_delete()
    {
        $recipe = Recipe::query()->whereHas('pages')->whereHas('chapters')->first();
        $this->assertNull($recipe->deleted_at);
        $pageCount = $recipe->pages()->count();
        $chapterCount = $recipe->chapters()->count();

        $deleteViewReq = $this->asEditor()->get($recipe->getUrl('/delete'));
        $deleteViewReq->assertSeeText('Are you sure you want to delete this recipe?');

        $deleteReq = $this->delete($recipe->getUrl());
        $deleteReq->assertRedirect(url('/recipes'));
        $this->assertActivityExists('recipe_delete', $recipe);

        $recipe->refresh();
        $this->assertNotNull($recipe->deleted_at);

        $this->assertTrue($recipe->pages()->count() === 0);
        $this->assertTrue($recipe->chapters()->count() === 0);
        $this->assertTrue($recipe->pages()->withTrashed()->count() === $pageCount);
        $this->assertTrue($recipe->chapters()->withTrashed()->count() === $chapterCount);
        $this->assertTrue($recipe->deletions()->count() === 1);

        $redirectReq = $this->get($deleteReq->baseResponse->headers->get('location'));
        $redirectReq->assertNotificationContains('Recipe Successfully Deleted');
    }

    public function test_cancel_on_create_page_leads_back_to_recipes_listing()
    {
        $resp = $this->asEditor()->get('/create-recipe');
        $resp->assertElementContains('form a[href="' . url('/recipes') . '"]', 'Cancel');
    }

    public function test_cancel_on_edit_recipe_page_leads_back_to_recipe()
    {
        /** @var Recipe $recipe */
        $recipe = Recipe::query()->first();
        $resp = $this->asEditor()->get($recipe->getUrl('/edit'));
        $resp->assertElementContains('form a[href="' . $recipe->getUrl() . '"]', 'Cancel');
    }

    public function test_next_previous_navigation_controls_show_within_recipe_content()
    {
        $recipe = Recipe::query()->first();
        $chapter = $recipe->chapters->first();

        $resp = $this->asEditor()->get($chapter->getUrl());
        $resp->assertElementContains('#sibling-navigation', 'Next');
        $resp->assertElementContains('#sibling-navigation', substr($chapter->pages[0]->name, 0, 20));

        $resp = $this->get($chapter->pages[0]->getUrl());
        $resp->assertElementContains('#sibling-navigation', substr($chapter->pages[1]->name, 0, 20));
        $resp->assertElementContains('#sibling-navigation', 'Previous');
        $resp->assertElementContains('#sibling-navigation', substr($chapter->name, 0, 20));
    }

    public function test_recently_viewed_recipes_updates_as_expected()
    {
        $recipes = Recipe::all()->take(2);

        $this->asAdmin()->get('/recipes')
            ->assertElementNotContains('#recents', $recipes[0]->name)
            ->assertElementNotContains('#recents', $recipes[1]->name);

        $this->get($recipes[0]->getUrl());
        $this->get($recipes[1]->getUrl());

        $this->get('/recipes')
            ->assertElementContains('#recents', $recipes[0]->name)
            ->assertElementContains('#recents', $recipes[1]->name);
    }

    public function test_popular_recipes_updates_upon_visits()
    {
        $recipes = Recipe::all()->take(2);

        $this->asAdmin()->get('/recipes')
            ->assertElementNotContains('#popular', $recipes[0]->name)
            ->assertElementNotContains('#popular', $recipes[1]->name);

        $this->get($recipes[0]->getUrl());
        $this->get($recipes[1]->getUrl());
        $this->get($recipes[0]->getUrl());

        $this->get('/recipes')
            ->assertElementContains('#popular .recipe:nth-child(1)', $recipes[0]->name)
            ->assertElementContains('#popular .recipe:nth-child(2)', $recipes[1]->name);
    }

    public function test_recipes_view_shows_view_toggle_option()
    {
        /** @var Recipe $recipe */
        $editor = $this->getEditor();
        setting()->putUser($editor, 'recipes_view_type', 'list');

        $resp = $this->actingAs($editor)->get('/recipes');
        $resp->assertElementContains('form[action$="/settings/users/' . $editor->id . '/switch-recipes-view"]', 'Grid View');
        $resp->assertElementExists('input[name="view_type"][value="grid"]');

        $resp = $this->patch("/settings/users/{$editor->id}/switch-recipes-view", ['view_type' => 'grid']);
        $resp->assertRedirect();
        $this->assertEquals('grid', setting()->getUser($editor, 'recipes_view_type'));

        $resp = $this->actingAs($editor)->get('/recipes');
        $resp->assertElementContains('form[action$="/settings/users/' . $editor->id . '/switch-recipes-view"]', 'List View');
        $resp->assertElementExists('input[name="view_type"][value="list"]');

        $resp = $this->patch("/settings/users/{$editor->id}/switch-recipes-view", ['view_type' => 'list']);
        $resp->assertRedirect();
        $this->assertEquals('list', setting()->getUser($editor, 'recipes_view_type'));
    }

    public function test_slug_multi_byte_url_safe()
    {
        $recipe = $this->newRecipe([
            'name' => 'информация',
        ]);

        $this->assertEquals('informaciya', $recipe->slug);

        $recipe = $this->newRecipe([
            'name' => '¿Qué?',
        ]);

        $this->assertEquals('que', $recipe->slug);
    }

    public function test_slug_format()
    {
        $recipe = $this->newRecipe([
            'name' => 'PartA / PartB / PartC',
        ]);

        $this->assertEquals('parta-partb-partc', $recipe->slug);
    }
}
