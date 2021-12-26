<?php

namespace Tests\Permissions;

use DailyRecipe\Auth\User;
use DailyRecipe\Entities\Models\Recipe;
use DailyRecipe\Entities\Models\Recipemenu;
use DailyRecipe\Entities\Models\Chapter;
use DailyRecipe\Entities\Models\Entity;
use DailyRecipe\Entities\Models\Page;
use Illuminate\Support\Str;
use Tests\TestCase;

class EntityPermissionsTest extends TestCase
{
    /**
     * @var User
     */
    protected $user;

    /**
     * @var User
     */
    protected $viewer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->getEditor();
        $this->viewer = $this->getViewer();
    }

    protected function setRestrictionsForTestRoles(Entity $entity, array $actions = [])
    {
        $roles = [
            $this->user->roles->first(),
            $this->viewer->roles->first(),
        ];
        $this->setEntityRestrictions($entity, $actions, $roles);
    }

    public function test_recipemenu_view_restriction()
    {
        /** @var Recipemenu $menu */
        $menu = Recipemenu::query()->first();

        $this->actingAs($this->user)
            ->get($menu->getUrl())
            ->assertStatus(200);

        $this->setRestrictionsForTestRoles($menu, []);

        $this->followingRedirects()->get($menu->getUrl())
            ->assertSee('Recipemenu not found');

        $this->setRestrictionsForTestRoles($menu, ['view']);

        $this->get($menu->getUrl())
            ->assertSee($menu->name);
    }

    public function test_recipemenu_update_restriction()
    {
        /** @var Recipemenu $menu */
        $menu = Recipemenu::query()->first();

        $this->actingAs($this->user)
            ->get($menu->getUrl('/edit'))
            ->assertSee('Edit Recipe');

        $this->setRestrictionsForTestRoles($menu, ['view', 'delete']);

        $resp = $this->get($menu->getUrl('/edit'))
            ->assertRedirect('/');
        $this->followRedirects($resp)->assertSee('You do not have permission');

        $this->setRestrictionsForTestRoles($menu, ['view', 'update']);

        $this->get($menu->getUrl('/edit'))
            ->assertOk();
    }

    public function test_recipemenu_delete_restriction()
    {
        /** @var Recipemenu $menu */
        $menu = Recipemenu::query()->first();

        $this->actingAs($this->user)
            ->get($menu->getUrl('/delete'))
            ->assertSee('Delete Recipe');

        $this->setRestrictionsForTestRoles($menu, ['view', 'update']);

        $this->get($menu->getUrl('/delete'))->assertRedirect('/');
        $this->get('/')->assertSee('You do not have permission');

        $this->setRestrictionsForTestRoles($menu, ['view', 'delete']);

        $this->get($menu->getUrl('/delete'))
            ->assertOk()
            ->assertSee('Delete Recipe');
    }

    public function test_recipe_view_restriction()
    {
        /** @var Recipe $recipe */
        $recipe = Recipe::query()->first();
        $recipePage = $recipe->pages->first();
        $recipeChapter = $recipe->chapters->first();

        $recipeUrl = $recipe->getUrl();
        $this->actingAs($this->user)
            ->get($recipeUrl)
            ->assertOk();

        $this->setRestrictionsForTestRoles($recipe, []);

        $this->followingRedirects()->get($recipeUrl)
            ->assertSee('Recipe not found');
        $this->followingRedirects()->get($recipePage->getUrl())
            ->assertSee('Page not found');
        $this->followingRedirects()->get($recipeChapter->getUrl())
            ->assertSee('Chapter not found');

        $this->setRestrictionsForTestRoles($recipe, ['view']);

        $this->get($recipeUrl)
            ->assertSee($recipe->name);
        $this->get($recipePage->getUrl())
            ->assertSee($recipePage->name);
        $this->get($recipeChapter->getUrl())
            ->assertSee($recipeChapter->name);
    }

    public function test_recipe_create_restriction()
    {
        /** @var Recipe $recipe */
        $recipe = Recipe::query()->first();

        $recipeUrl = $recipe->getUrl();
        $this->actingAs($this->viewer)
            ->get($recipeUrl)
            ->assertElementNotContains('.actions', 'New Page')
            ->assertElementNotContains('.actions', 'New Chapter');
        $this->actingAs($this->user)
            ->get($recipeUrl)
            ->assertElementContains('.actions', 'New Page')
            ->assertElementContains('.actions', 'New Chapter');

        $this->setRestrictionsForTestRoles($recipe, ['view', 'delete', 'update']);

        $this->get($recipeUrl . '/create-chapter')->assertRedirect('/');
        $this->get('/')->assertSee('You do not have permission');

        $this->get($recipeUrl . '/create-page')->assertRedirect('/');
        $this->get('/')->assertSee('You do not have permission');

        $this->get($recipeUrl)
            ->assertElementNotContains('.actions', 'New Page')
            ->assertElementNotContains('.actions', 'New Chapter');

        $this->setRestrictionsForTestRoles($recipe, ['view', 'create']);

        $resp = $this->post($recipe->getUrl('/create-chapter'), [
            'name'        => 'test chapter',
            'description' => 'desc',
        ]);
        $resp->assertRedirect($recipe->getUrl('/chapter/test-chapter'));

        $this->get($recipe->getUrl('/create-page'));
        /** @var Page $page */
        $page = Page::query()->where('draft', '=', true)->orderBy('id', 'desc')->first();
        $resp = $this->post($page->getUrl(), [
            'name' => 'test page',
            'html' => 'test content',
        ]);
        $resp->assertRedirect($recipe->getUrl('/page/test-page'));

        $this->get($recipeUrl)
            ->assertElementContains('.actions', 'New Page')
            ->assertElementContains('.actions', 'New Chapter');
    }

    public function test_recipe_update_restriction()
    {
        /** @var Recipe $recipe */
        $recipe = Recipe::query()->first();
        $recipePage = $recipe->pages->first();
        $recipeChapter = $recipe->chapters->first();

        $recipeUrl = $recipe->getUrl();
        $this->actingAs($this->user)
            ->get($recipeUrl . '/edit')
            ->assertSee('Edit Recipe');

        $this->setRestrictionsForTestRoles($recipe, ['view', 'delete']);

        $this->get($recipeUrl . '/edit')->assertRedirect('/');
        $this->get('/')->assertSee('You do not have permission');
        $this->get($recipePage->getUrl() . '/edit')->assertRedirect('/');
        $this->get('/')->assertSee('You do not have permission');
        $this->get($recipeChapter->getUrl() . '/edit')->assertRedirect('/');
        $this->get('/')->assertSee('You do not have permission');

        $this->setRestrictionsForTestRoles($recipe, ['view', 'update']);

        $this->get($recipeUrl . '/edit')->assertOk();
        $this->get($recipePage->getUrl() . '/edit')->assertOk();
        $this->get($recipeChapter->getUrl() . '/edit')->assertSee('Edit Chapter');
    }

    public function test_recipe_delete_restriction()
    {
        /** @var Recipe $recipe */
        $recipe = Recipe::query()->first();
        $recipePage = $recipe->pages->first();
        $recipeChapter = $recipe->chapters->first();

        $recipeUrl = $recipe->getUrl();
        $this->actingAs($this->user)->get($recipeUrl . '/delete')
            ->assertSee('Delete Recipe');

        $this->setRestrictionsForTestRoles($recipe, ['view', 'update']);

        $this->get($recipeUrl . '/delete')->assertRedirect('/');
        $this->get('/')->assertSee('You do not have permission');
        $this->get($recipePage->getUrl() . '/delete')->assertRedirect('/');
        $this->get('/')->assertSee('You do not have permission');
        $this->get($recipeChapter->getUrl() . '/delete')->assertRedirect('/');
        $this->get('/')->assertSee('You do not have permission');

        $this->setRestrictionsForTestRoles($recipe, ['view', 'delete']);

        $this->get($recipeUrl . '/delete')->assertOk()->assertSee('Delete Recipe');
        $this->get($recipePage->getUrl('/delete'))->assertOk()->assertSee('Delete Page');
        $this->get($recipeChapter->getUrl('/delete'))->assertSee('Delete Chapter');
    }

    public function test_chapter_view_restriction()
    {
        /** @var Chapter $chapter */
        $chapter = Chapter::query()->first();
        $chapterPage = $chapter->pages->first();

        $chapterUrl = $chapter->getUrl();
        $this->actingAs($this->user)->get($chapterUrl)->assertOk();

        $this->setRestrictionsForTestRoles($chapter, []);

        $this->followingRedirects()->get($chapterUrl)->assertSee('Chapter not found');
        $this->followingRedirects()->get($chapterPage->getUrl())->assertSee('Page not found');

        $this->setRestrictionsForTestRoles($chapter, ['view']);

        $this->get($chapterUrl)->assertSee($chapter->name);
        $this->get($chapterPage->getUrl())->assertSee($chapterPage->name);
    }

    public function test_chapter_create_restriction()
    {
        /** @var Chapter $chapter */
        $chapter = Chapter::query()->first();

        $chapterUrl = $chapter->getUrl();
        $this->actingAs($this->user)
            ->get($chapterUrl)
            ->assertElementContains('.actions', 'New Page');

        $this->setRestrictionsForTestRoles($chapter, ['view', 'delete', 'update']);

        $this->get($chapterUrl . '/create-page')->assertRedirect('/');
        $this->get('/')->assertSee('You do not have permission');
        $this->get($chapterUrl)->assertElementNotContains('.actions', 'New Page');

        $this->setRestrictionsForTestRoles($chapter, ['view', 'create']);

        $this->get($chapter->getUrl('/create-page'));
        /** @var Page $page */
        $page = Page::query()->where('draft', '=', true)->orderBy('id', 'desc')->first();
        $resp = $this->post($page->getUrl(), [
            'name' => 'test page',
            'html' => 'test content',
        ]);
        $resp->assertRedirect($chapter->recipe->getUrl('/page/test-page'));

        $this->get($chapterUrl)->assertElementContains('.actions', 'New Page');
    }

    public function test_chapter_update_restriction()
    {
        /** @var Chapter $chapter */
        $chapter = Chapter::query()->first();
        $chapterPage = $chapter->pages->first();

        $chapterUrl = $chapter->getUrl();
        $this->actingAs($this->user)->get($chapterUrl . '/edit')
            ->assertSee('Edit Chapter');

        $this->setRestrictionsForTestRoles($chapter, ['view', 'delete']);

        $this->get($chapterUrl . '/edit')->assertRedirect('/');
        $this->get('/')->assertSee('You do not have permission');
        $this->get($chapterPage->getUrl() . '/edit')->assertRedirect('/');
        $this->get('/')->assertSee('You do not have permission');

        $this->setRestrictionsForTestRoles($chapter, ['view', 'update']);

        $this->get($chapterUrl . '/edit')->assertOk()->assertSee('Edit Chapter');
        $this->get($chapterPage->getUrl() . '/edit')->assertOk();
    }

    public function test_chapter_delete_restriction()
    {
        /** @var Chapter $chapter */
        $chapter = Chapter::query()->first();
        $chapterPage = $chapter->pages->first();

        $chapterUrl = $chapter->getUrl();
        $this->actingAs($this->user)
            ->get($chapterUrl . '/delete')
            ->assertSee('Delete Chapter');

        $this->setRestrictionsForTestRoles($chapter, ['view', 'update']);

        $this->get($chapterUrl . '/delete')->assertRedirect('/');
        $this->get('/')->assertSee('You do not have permission');
        $this->get($chapterPage->getUrl() . '/delete')->assertRedirect('/');
        $this->get('/')->assertSee('You do not have permission');

        $this->setRestrictionsForTestRoles($chapter, ['view', 'delete']);

        $this->get($chapterUrl . '/delete')->assertOk()->assertSee('Delete Chapter');
        $this->get($chapterPage->getUrl() . '/delete')->assertOk()->assertSee('Delete Page');
    }

    public function test_page_view_restriction()
    {
        /** @var Page $page */
        $page = Page::query()->first();

        $pageUrl = $page->getUrl();
        $this->actingAs($this->user)->get($pageUrl)->assertOk();

        $this->setRestrictionsForTestRoles($page, ['update', 'delete']);

        $this->get($pageUrl)->assertSee('Page not found');

        $this->setRestrictionsForTestRoles($page, ['view']);

        $this->get($pageUrl)->assertSee($page->name);
    }

    public function test_page_update_restriction()
    {
        /** @var Page $page */
        $page = Page::query()->first();

        $pageUrl = $page->getUrl();
        $this->actingAs($this->user)
            ->get($pageUrl . '/edit')
            ->assertElementExists('input[name="name"][value="' . $page->name . '"]');

        $this->setRestrictionsForTestRoles($page, ['view', 'delete']);

        $this->get($pageUrl . '/edit')->assertRedirect('/');
        $this->get('/')->assertSee('You do not have permission');

        $this->setRestrictionsForTestRoles($page, ['view', 'update']);

        $this->get($pageUrl . '/edit')
            ->assertOk()
            ->assertElementExists('input[name="name"][value="' . $page->name . '"]');
    }

    public function test_page_delete_restriction()
    {
        /** @var Page $page */
        $page = Page::query()->first();

        $pageUrl = $page->getUrl();
        $this->actingAs($this->user)
            ->get($pageUrl . '/delete')
            ->assertSee('Delete Page');

        $this->setRestrictionsForTestRoles($page, ['view', 'update']);

        $this->get($pageUrl . '/delete')->assertRedirect('/');
        $this->get('/')->assertSee('You do not have permission');

        $this->setRestrictionsForTestRoles($page, ['view', 'delete']);

        $this->get($pageUrl . '/delete')->assertOk()->assertSee('Delete Page');
    }

    protected function entityRestrictionFormTest(string $model, string $title, string $permission, string $roleId)
    {
        /** @var Entity $modelInstance */
        $modelInstance = $model::query()->first();
        $this->asAdmin()->get($modelInstance->getUrl('/permissions'))
            ->assertSee($title);

        $this->put($modelInstance->getUrl('/permissions'), [
            'restricted'   => 'true',
            'restrictions' => [
                $roleId => [
                    $permission => 'true',
                ],
            ],
        ]);

        $this->assertDatabaseHas($modelInstance->getTable(), ['id' => $modelInstance->id, 'restricted' => true]);
        $this->assertDatabaseHas('entity_permissions', [
            'restrictable_id'   => $modelInstance->id,
            'restrictable_type' => $modelInstance->getMorphClass(),
            'role_id'           => $roleId,
            'action'            => $permission,
        ]);
    }

    public function test_recipemenu_restriction_form()
    {
        $this->entityRestrictionFormTest(Recipemenu::class, 'Recipemenu Permissions', 'view', '2');
    }

    public function test_recipe_restriction_form()
    {
        $this->entityRestrictionFormTest(Recipe::class, 'Recipe Permissions', 'view', '2');
    }

    public function test_chapter_restriction_form()
    {
        $this->entityRestrictionFormTest(Chapter::class, 'Chapter Permissions', 'update', '2');
    }

    public function test_page_restriction_form()
    {
        $this->entityRestrictionFormTest(Page::class, 'Page Permissions', 'delete', '2');
    }

    public function test_restricted_pages_not_visible_in_recipe_navigation_on_pages()
    {
        /** @var Chapter $chapter */
        $chapter = Chapter::query()->first();
        $page = $chapter->pages->first();
        $page2 = $chapter->pages[2];

        $this->setRestrictionsForTestRoles($page, []);

        $this->actingAs($this->user)
            ->get($page2->getUrl())
            ->assertElementNotContains('.sidebar-page-list', $page->name);
    }

    public function test_restricted_pages_not_visible_in_recipe_navigation_on_chapters()
    {
        /** @var Chapter $chapter */
        $chapter = Chapter::query()->first();
        $page = $chapter->pages->first();

        $this->setRestrictionsForTestRoles($page, []);

        $this->actingAs($this->user)
            ->get($chapter->getUrl())
            ->assertElementNotContains('.sidebar-page-list', $page->name);
    }

    public function test_restricted_pages_not_visible_on_chapter_pages()
    {
        /** @var Chapter $chapter */
        $chapter = Chapter::query()->first();
        $page = $chapter->pages->first();

        $this->setRestrictionsForTestRoles($page, []);

        $this->actingAs($this->user)
            ->get($chapter->getUrl())
            ->assertDontSee($page->name);
    }

    public function test_restricted_chapter_pages_not_visible_on_recipe_page()
    {
        /** @var Chapter $chapter */
        $chapter = Chapter::query()->first();
        $this->actingAs($this->user)
            ->get($chapter->recipe->getUrl())
            ->assertSee($chapter->pages->first()->name);

        foreach ($chapter->pages as $page) {
            $this->setRestrictionsForTestRoles($page, []);
        }

        $this->actingAs($this->user)
            ->get($chapter->recipe->getUrl())
            ->assertDontSee($chapter->pages->first()->name);
    }

    public function test_recipemenu_update_restriction_override()
    {
        /** @var Recipemenu $menu */
        $menu = Recipemenu::query()->first();

        $this->actingAs($this->viewer)
            ->get($menu->getUrl('/edit'))
            ->assertDontSee('Edit Recipe');

        $this->setRestrictionsForTestRoles($menu, ['view', 'delete']);

        $this->get($menu->getUrl('/edit'))->assertRedirect('/');
        $this->get('/')->assertSee('You do not have permission');

        $this->setRestrictionsForTestRoles($menu, ['view', 'update']);

        $this->get($menu->getUrl('/edit'))->assertOk();
    }

    public function test_recipemenu_delete_restriction_override()
    {
        /** @var Recipemenu $menu */
        $menu = Recipemenu::query()->first();

        $this->actingAs($this->viewer)
            ->get($menu->getUrl('/delete'))
            ->assertDontSee('Delete Recipe');

        $this->setRestrictionsForTestRoles($menu, ['view', 'update']);

        $this->get($menu->getUrl('/delete'))->assertRedirect('/');
        $this->get('/')->assertSee('You do not have permission');

        $this->setRestrictionsForTestRoles($menu, ['view', 'delete']);

        $this->get($menu->getUrl('/delete'))->assertOk()->assertSee('Delete Recipe');
    }

    public function test_recipe_create_restriction_override()
    {
        /** @var Recipe $recipe */
        $recipe = Recipe::query()->first();

        $recipeUrl = $recipe->getUrl();
        $this->actingAs($this->viewer)
            ->get($recipeUrl)
            ->assertElementNotContains('.actions', 'New Page')
            ->assertElementNotContains('.actions', 'New Chapter');

        $this->setRestrictionsForTestRoles($recipe, ['view', 'delete', 'update']);

        $this->get($recipeUrl . '/create-chapter')->assertRedirect('/');
        $this->get('/')->assertSee('You do not have permission');
        $this->get($recipeUrl . '/create-page')->assertRedirect('/');
        $this->get('/')->assertSee('You do not have permission');
        $this->get($recipeUrl)->assertElementNotContains('.actions', 'New Page')
            ->assertElementNotContains('.actions', 'New Chapter');

        $this->setRestrictionsForTestRoles($recipe, ['view', 'create']);

        $resp = $this->post($recipe->getUrl('/create-chapter'), [
            'name'        => 'test chapter',
            'description' => 'test desc',
        ]);
        $resp->assertRedirect($recipe->getUrl('/chapter/test-chapter'));

        $this->get($recipe->getUrl('/create-page'));
        /** @var Page $page */
        $page = Page::query()->where('draft', '=', true)->orderByDesc('id')->first();
        $resp = $this->post($page->getUrl(), [
            'name' => 'test page',
            'html' => 'test desc',
        ]);
        $resp->assertRedirect($recipe->getUrl('/page/test-page'));

        $this->get($recipeUrl)
            ->assertElementContains('.actions', 'New Page')
            ->assertElementContains('.actions', 'New Chapter');
    }

    public function test_recipe_update_restriction_override()
    {
        /** @var Recipe $recipe */
        $recipe = Recipe::query()->first();
        $recipePage = $recipe->pages->first();
        $recipeChapter = $recipe->chapters->first();

        $recipeUrl = $recipe->getUrl();
        $this->actingAs($this->viewer)->get($recipeUrl . '/edit')
            ->assertDontSee('Edit Recipe');

        $this->setRestrictionsForTestRoles($recipe, ['view', 'delete']);

        $this->get($recipeUrl . '/edit')->assertRedirect('/');
        $this->get('/')->assertSee('You do not have permission');
        $this->get($recipePage->getUrl() . '/edit')->assertRedirect('/');
        $this->get('/')->assertSee('You do not have permission');
        $this->get($recipeChapter->getUrl() . '/edit')->assertRedirect('/');
        $this->get('/')->assertSee('You do not have permission');

        $this->setRestrictionsForTestRoles($recipe, ['view', 'update']);

        $this->get($recipeUrl . '/edit')->assertOk();
        $this->get($recipePage->getUrl() . '/edit')->assertOk();
        $this->get($recipeChapter->getUrl() . '/edit')->assertSee('Edit Chapter');
    }

    public function test_recipe_delete_restriction_override()
    {
        /** @var Recipe $recipe */
        $recipe = Recipe::query()->first();
        $recipePage = $recipe->pages->first();
        $recipeChapter = $recipe->chapters->first();

        $recipeUrl = $recipe->getUrl();
        $this->actingAs($this->viewer)
            ->get($recipeUrl . '/delete')
            ->assertDontSee('Delete Recipe');

        $this->setRestrictionsForTestRoles($recipe, ['view', 'update']);

        $this->get($recipeUrl . '/delete')->assertRedirect('/');
        $this->get('/')->assertSee('You do not have permission');
        $this->get($recipePage->getUrl() . '/delete')->assertRedirect('/');
        $this->get('/')->assertSee('You do not have permission');
        $this->get($recipeChapter->getUrl() . '/delete')->assertRedirect('/');
        $this->get('/')->assertSee('You do not have permission');

        $this->setRestrictionsForTestRoles($recipe, ['view', 'delete']);

        $this->get($recipeUrl . '/delete')->assertOk()->assertSee('Delete Recipe');
        $this->get($recipePage->getUrl() . '/delete')->assertOk()->assertSee('Delete Page');
        $this->get($recipeChapter->getUrl() . '/delete')->assertSee('Delete Chapter');
    }

    public function test_page_visible_if_has_permissions_when_recipe_not_visible()
    {
        /** @var Recipe $recipe */
        $recipe = Recipe::query()->first();
        $recipeChapter = $recipe->chapters->first();
        $recipePage = $recipeChapter->pages->first();

        foreach ([$recipe, $recipeChapter, $recipePage] as $entity) {
            $entity->name = Str::random(24);
            $entity->save();
        }

        $this->setRestrictionsForTestRoles($recipe, []);
        $this->setRestrictionsForTestRoles($recipePage, ['view']);

        $this->actingAs($this->viewer);
        $resp = $this->get($recipePage->getUrl());
        $resp->assertOk();
        $resp->assertSee($recipePage->name);
        $resp->assertDontSee(substr($recipe->name, 0, 15));
        $resp->assertDontSee(substr($recipeChapter->name, 0, 15));
    }

    public function test_recipe_sort_view_permission()
    {
        /** @var Recipe $firstRecipe */
        $firstRecipe = Recipe::query()->first();
        /** @var Recipe $secondRecipe */
        $secondRecipe = Recipe::query()->find(2);

        $this->setRestrictionsForTestRoles($firstRecipe, ['view', 'update']);
        $this->setRestrictionsForTestRoles($secondRecipe, ['view']);

        // Test sort page visibility
        $this->actingAs($this->user)->get($secondRecipe->getUrl('/sort'))->assertRedirect('/');
        $this->get('/')->assertSee('You do not have permission');

        // Check sort page on first recipe
        $this->actingAs($this->user)->get($firstRecipe->getUrl('/sort'));
    }

    public function test_recipe_sort_permission()
    {
        /** @var Recipe $firstRecipe */
        $firstRecipe = Recipe::query()->first();
        /** @var Recipe $secondRecipe */
        $secondRecipe = Recipe::query()->find(2);

        $this->setRestrictionsForTestRoles($firstRecipe, ['view', 'update']);
        $this->setRestrictionsForTestRoles($secondRecipe, ['view']);

        $firstRecipeChapter = $this->newChapter(['name' => 'first recipe chapter'], $firstRecipe);
        $secondRecipeChapter = $this->newChapter(['name' => 'second recipe chapter'], $secondRecipe);

        // Create request data
        $reqData = [
            [
                'id'            => $firstRecipeChapter->id,
                'sort'          => 0,
                'parentChapter' => false,
                'type'          => 'chapter',
                'recipe'          => $secondRecipe->id,
            ],
        ];

        // Move chapter from first recipe to a second recipe
        $this->actingAs($this->user)->put($firstRecipe->getUrl() . '/sort', ['sort-tree' => json_encode($reqData)])
            ->assertRedirect('/');
        $this->get('/')->assertSee('You do not have permission');

        $reqData = [
            [
                'id'            => $secondRecipeChapter->id,
                'sort'          => 0,
                'parentChapter' => false,
                'type'          => 'chapter',
                'recipe'          => $firstRecipe->id,
            ],
        ];

        // Move chapter from second recipe to first recipe
        $this->actingAs($this->user)->put($firstRecipe->getUrl() . '/sort', ['sort-tree' => json_encode($reqData)])
                ->assertRedirect('/');
        $this->get('/')->assertSee('You do not have permission');
    }

    public function test_can_create_page_if_chapter_has_permissions_when_recipe_not_visible()
    {
        /** @var Recipe $recipe */
        $recipe = Recipe::query()->first();
        $this->setRestrictionsForTestRoles($recipe, []);
        $recipeChapter = $recipe->chapters->first();
        $this->setRestrictionsForTestRoles($recipeChapter, ['view']);

        $this->actingAs($this->user)->get($recipeChapter->getUrl())
            ->assertDontSee('New Page');

        $this->setRestrictionsForTestRoles($recipeChapter, ['view', 'create']);

        $this->get($recipeChapter->getUrl('/create-page'));
        /** @var Page $page */
        $page = Page::query()->where('draft', '=', true)->orderByDesc('id')->first();
        $resp = $this->post($page->getUrl(), [
            'name' => 'test page',
            'html' => 'test content',
        ]);
        $resp->assertRedirect($recipe->getUrl('/page/test-page'));
    }
}
