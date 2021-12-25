<?php

namespace Tests\Entity;

use DailyRecipe\Auth\User;
use DailyRecipe\Entities\Models\Recipe;
use DailyRecipe\Entities\Models\Recipemenu;
use DailyRecipe\Uploads\Image;
use Illuminate\Support\Str;
use Tests\TestCase;
use Tests\Uploads\UsesImages;

class RecipeMenuTest extends TestCase
{
    use UsesImages;

    public function test_menus_shows_in_header_if_have_view_permissions()
    {
        $viewer = $this->getViewer();
        $resp = $this->actingAs($viewer)->get('/');
        $resp->assertElementContains('header', 'Menus');

        $viewer->roles()->delete();
        $this->giveUserPermissions($viewer);
        $resp = $this->actingAs($viewer)->get('/');
        $resp->assertElementNotContains('header', 'Menus');

        $this->giveUserPermissions($viewer, ['recipemenu-view-all']);
        $resp = $this->actingAs($viewer)->get('/');
        $resp->assertElementContains('header', 'Menus');

        $viewer->roles()->delete();
        $this->giveUserPermissions($viewer, ['recipemenu-view-own']);
        $resp = $this->actingAs($viewer)->get('/');
        $resp->assertElementContains('header', 'Menus');
    }

    public function test_menus_shows_in_header_if_have_any_menus_view_permission()
    {
        $user = User::factory()->create();
        $this->giveUserPermissions($user, ['image-create-all']);
        $menu = Recipemenu::first();
        $userRole = $user->roles()->first();

        $resp = $this->actingAs($user)->get('/');
        $resp->assertElementNotContains('header', 'Menus');

        $this->setEntityRestrictions($menu, ['view'], [$userRole]);

        $resp = $this->get('/');
        $resp->assertElementContains('header', 'Menus');
    }

    public function test_menus_page_contains_create_link()
    {
        $resp = $this->asEditor()->get('/menus');
        $resp->assertElementContains('a', 'New Menu');
    }

    public function test_book_not_visible_in_menu_list_view_if_user_cant_view_menu()
    {
        config()->set([
            'setting-defaults.user.recipemenus_view_type' => 'list',
        ]);
        $menu = Recipemenu::query()->first();
        $book = $menu->books()->first();

        $resp = $this->asEditor()->get('/menus');
        $resp->assertSee($book->name);
        $resp->assertSee($book->getUrl());

        $this->setEntityRestrictions($book, []);

        $resp = $this->asEditor()->get('/menus');
        $resp->assertDontSee($book->name);
        $resp->assertDontSee($book->getUrl());
    }

    public function test_menus_create()
    {
        $booksToInclude = Recipe::take(2)->get();
        $menuInfo = [
            'name'        => 'My test book' . Str::random(4),
            'description' => 'Test book description ' . Str::random(10),
        ];
        $resp = $this->asEditor()->post('/menus', array_merge($menuInfo, [
            'recipes' => $booksToInclude->implode('id', ','),
            'tags'  => [
                [
                    'name'  => 'Test Category',
                    'value' => 'Test Tag Value',
                ],
            ],
        ]));
        $resp->assertRedirect();
        $editorId = $this->getEditor()->id;
        $this->assertDatabaseHas('recipemenus', array_merge($menuInfo, ['created_by' => $editorId, 'updated_by' => $editorId]));

        $menu = Recipemenu::where('name', '=', $menuInfo['name'])->first();
        $menuPage = $this->get($menu->getUrl());
        $menuPage->assertSee($menuInfo['name']);
        $menuPage->assertSee($menuInfo['description']);
        $menuPage->assertElementContains('.tag-item', 'Test Category');
        $menuPage->assertElementContains('.tag-item', 'Test Tag Value');

        $this->assertDatabaseHas('recipemenus_books', ['recipemenu_id' => $menu->id, 'recipe_id' => $booksToInclude[0]->id]);
        $this->assertDatabaseHas('recipemenus_books', ['recipemenu_id' => $menu->id, 'recipe_id' => $booksToInclude[1]->id]);
    }

    public function test_menus_create_sets_cover_image()
    {
        $menuInfo = [
            'name'        => 'My test book' . Str::random(4),
            'description' => 'Test book description ' . Str::random(10),
        ];

        $imageFile = $this->getTestImage('menu-test.png');
        $resp = $this->asEditor()->call('POST', '/menus', $menuInfo, [], ['image' => $imageFile]);
        $resp->assertRedirect();

        $lastImage = Image::query()->orderByDesc('id')->firstOrFail();
        $menu = Recipemenu::query()->where('name', '=', $menuInfo['name'])->first();
        $this->assertDatabaseHas('recipemenus', [
            'id'       => $menu->id,
            'image_id' => $lastImage->id,
        ]);
        $this->assertEquals($lastImage->id, $menu->cover->id);
    }

    public function test_menu_view()
    {
        $menu = Recipemenu::first();
        $resp = $this->asEditor()->get($menu->getUrl());
        $resp->assertStatus(200);
        $resp->assertSeeText($menu->name);
        $resp->assertSeeText($menu->description);

        foreach ($menu->books as $book) {
            $resp->assertSee($book->name);
        }
    }

    public function test_menu_view_shows_action_buttons()
    {
        $menu = Recipemenu::first();
        $resp = $this->asAdmin()->get($menu->getUrl());
        $resp->assertSee($menu->getUrl('/create-book'));
        $resp->assertSee($menu->getUrl('/edit'));
        $resp->assertSee($menu->getUrl('/permissions'));
        $resp->assertSee($menu->getUrl('/delete'));
        $resp->assertElementContains('a', 'New Recipe');
        $resp->assertElementContains('a', 'Edit');
        $resp->assertElementContains('a', 'Permissions');
        $resp->assertElementContains('a', 'Delete');

        $resp = $this->asEditor()->get($menu->getUrl());
        $resp->assertDontSee($menu->getUrl('/permissions'));
    }

    public function test_menu_view_has_sort_control_that_defaults_to_default()
    {
        $menu = Recipemenu::query()->first();
        $resp = $this->asAdmin()->get($menu->getUrl());
        $resp->assertElementExists('form[action$="change-sort/menu_books"]');
        $resp->assertElementContains('form[action$="change-sort/menu_books"] [aria-haspopup="true"]', 'Default');
    }

    public function test_menu_view_sort_takes_action()
    {
        $menu = Recipemenu::query()->whereHas('recipes')->with('recipes')->first();
        $books = Recipe::query()->take(3)->get(['id', 'name']);
        $books[0]->fill(['name' => 'bsfsdfsdfsd'])->save();
        $books[1]->fill(['name' => 'adsfsdfsdfsd'])->save();
        $books[2]->fill(['name' => 'hdgfgdfg'])->save();

        // Set book ordering
        $this->asAdmin()->put($menu->getUrl(), [
            'recipes' => $books->implode('id', ','),
            'tags'  => [], 'description' => 'abc', 'name' => 'abc',
        ]);
        $this->assertEquals(3, $menu->books()->count());
        $menu->refresh();

        $resp = $this->asEditor()->get($menu->getUrl());
        $resp->assertElementContains('.book-content a.grid-card', $books[0]->name, 1);
        $resp->assertElementNotContains('.book-content a.grid-card', $books[0]->name, 3);

        setting()->putUser($this->getEditor(), 'menu_books_sort_order', 'desc');
        $resp = $this->asEditor()->get($menu->getUrl());
        $resp->assertElementNotContains('.book-content a.grid-card', $books[0]->name, 1);
        $resp->assertElementContains('.book-content a.grid-card', $books[0]->name, 3);

        setting()->putUser($this->getEditor(), 'menu_books_sort_order', 'desc');
        setting()->putUser($this->getEditor(), 'menu_books_sort', 'name');
        $resp = $this->asEditor()->get($menu->getUrl());
        $resp->assertElementContains('.book-content a.grid-card', 'hdgfgdfg', 1);
        $resp->assertElementContains('.book-content a.grid-card', 'bsfsdfsdfsd', 2);
        $resp->assertElementContains('.book-content a.grid-card', 'adsfsdfsdfsd', 3);
    }

    public function test_menu_edit()
    {
        $menu = Recipemenu::first();
        $resp = $this->asEditor()->get($menu->getUrl('/edit'));
        $resp->assertSeeText('Edit Recipemenu');

        $booksToInclude = Recipe::take(2)->get();
        $menuInfo = [
            'name'        => 'My test book' . Str::random(4),
            'description' => 'Test book description ' . Str::random(10),
        ];

        $resp = $this->asEditor()->put($menu->getUrl(), array_merge($menuInfo, [
            'recipes' => $booksToInclude->implode('id', ','),
            'tags'  => [
                [
                    'name'  => 'Test Category',
                    'value' => 'Test Tag Value',
                ],
            ],
        ]));
        $menu = Recipemenu::find($menu->id);
        $resp->assertRedirect($menu->getUrl());
        $this->assertSessionHas('success');

        $editorId = $this->getEditor()->id;
        $this->assertDatabaseHas('recipemenus', array_merge($menuInfo, ['id' => $menu->id, 'created_by' => $editorId, 'updated_by' => $editorId]));

        $menuPage = $this->get($menu->getUrl());
        $menuPage->assertSee($menuInfo['name']);
        $menuPage->assertSee($menuInfo['description']);
        $menuPage->assertElementContains('.tag-item', 'Test Category');
        $menuPage->assertElementContains('.tag-item', 'Test Tag Value');

        $this->assertDatabaseHas('recipemenus_books', ['recipemenu_id' => $menu->id, 'recipe_id' => $booksToInclude[0]->id]);
        $this->assertDatabaseHas('recipemenus_books', ['recipemenu_id' => $menu->id, 'recipe_id' => $booksToInclude[1]->id]);
    }

    public function test_menu_create_new_book()
    {
        $menu = Recipemenu::first();
        $resp = $this->asEditor()->get($menu->getUrl('/create-book'));

        $resp->assertSee('Create New Recipe');
        $resp->assertSee($menu->getShortName());

        $testName = 'Test Recipe in Menu Name';

        $createBookResp = $this->asEditor()->post($menu->getUrl('/create-book'), [
            'name'        => $testName,
            'description' => 'Recipe in menu description',
        ]);
        $createBookResp->assertRedirect();

        $newBook = Recipe::query()->orderBy('id', 'desc')->first();
        $this->assertDatabaseHas('recipemenus_books', [
            'recipemenu_id' => $menu->id,
            'recipe_id'      => $newBook->id,
        ]);

        $resp = $this->asEditor()->get($menu->getUrl());
        $resp->assertSee($testName);
    }

    public function test_menu_delete()
    {
        $menu = Recipemenu::query()->whereHas('recipes')->first();
        $this->assertNull($menu->deleted_at);
        $bookCount = $menu->books()->count();

        $deleteViewReq = $this->asEditor()->get($menu->getUrl('/delete'));
        $deleteViewReq->assertSeeText('Are you sure you want to delete this recipemenu?');

        $deleteReq = $this->delete($menu->getUrl());
        $deleteReq->assertRedirect(url('/menus'));
        $this->assertActivityExists('menu_delete', $menu);

        $menu->refresh();
        $this->assertNotNull($menu->deleted_at);

        $this->assertTrue($menu->books()->count() === $bookCount);
        $this->assertTrue($menu->deletions()->count() === 1);

        $redirectReq = $this->get($deleteReq->baseResponse->headers->get('location'));
        $redirectReq->assertNotificationContains('Recipemenu Successfully Deleted');
    }

    public function test_menu_copy_permissions()
    {
        $menu = Recipemenu::first();
        $resp = $this->asAdmin()->get($menu->getUrl('/permissions'));
        $resp->assertSeeText('Copy Permissions');
        $resp->assertSee("action=\"{$menu->getUrl('/copy-permissions')}\"", false);

        $child = $menu->books()->first();
        $editorRole = $this->getEditor()->roles()->first();
        $this->assertFalse(boolval($child->restricted), 'Child book should not be restricted by default');
        $this->assertTrue($child->permissions()->count() === 0, 'Child book should have no permissions by default');

        $this->setEntityRestrictions($menu, ['view', 'update'], [$editorRole]);
        $resp = $this->post($menu->getUrl('/copy-permissions'));
        $child = $menu->books()->first();

        $resp->assertRedirect($menu->getUrl());
        $this->assertTrue(boolval($child->restricted), 'Child book should now be restricted');
        $this->assertTrue($child->permissions()->count() === 2, 'Child book should have copied permissions');
        $this->assertDatabaseHas('entity_permissions', ['restrictable_id' => $child->id, 'action' => 'view', 'role_id' => $editorRole->id]);
        $this->assertDatabaseHas('entity_permissions', ['restrictable_id' => $child->id, 'action' => 'update', 'role_id' => $editorRole->id]);
    }

    public function test_permission_page_has_a_warning_about_no_cascading()
    {
        $menu = Recipemenu::first();
        $resp = $this->asAdmin()->get($menu->getUrl('/permissions'));
        $resp->assertSeeText('Permissions on recipemenus do not automatically cascade to contained recipes.');
    }

    public function test_recipemenus_show_in_breadcrumbs_if_in_context()
    {
        $menu = Recipemenu::first();
        $menuBook = $menu->books()->first();
        $menuPage = $menuBook->pages()->first();
        $this->asAdmin();

        $bookVisit = $this->get($menuBook->getUrl());
        $bookVisit->assertElementNotContains('.breadcrumbs', 'Menus');
        $bookVisit->assertElementNotContains('.breadcrumbs', $menu->getShortName());

        $this->get($menu->getUrl());
        $bookVisit = $this->get($menuBook->getUrl());
        $bookVisit->assertElementContains('.breadcrumbs', 'Menus');
        $bookVisit->assertElementContains('.breadcrumbs', $menu->getShortName());

        $pageVisit = $this->get($menuPage->getUrl());
        $pageVisit->assertElementContains('.breadcrumbs', 'Menus');
        $pageVisit->assertElementContains('.breadcrumbs', $menu->getShortName());

        $this->get('/recipes');
        $pageVisit = $this->get($menuPage->getUrl());
        $pageVisit->assertElementNotContains('.breadcrumbs', 'Menus');
        $pageVisit->assertElementNotContains('.breadcrumbs', $menu->getShortName());
    }

    public function test_recipemenus_show_on_book()
    {
        // Create menu
        $menuInfo = [
            'name'        => 'My test menu' . Str::random(4),
            'description' => 'Test menu description ' . Str::random(10),
        ];

        $this->asEditor()->post('/menus', $menuInfo);
        $menu = Recipemenu::where('name', '=', $menuInfo['name'])->first();

        // Create book and add to menu
        $this->asEditor()->post($menu->getUrl('/create-book'), [
            'name'        => 'Test book name',
            'description' => 'Recipe in menu description',
        ]);

        $newBook = Recipe::query()->orderBy('id', 'desc')->first();

        $resp = $this->asEditor()->get($newBook->getUrl());
        $resp->assertElementContains('.tri-layout-left-contents', $menuInfo['name']);

        // Remove menu
        $this->delete($menu->getUrl());

        $resp = $this->asEditor()->get($newBook->getUrl());
        $resp->assertDontSee($menuInfo['name']);
    }

    public function test_cancel_on_child_book_creation_returns_to_original_menu()
    {
        /** @var Recipemenu $menu */
        $menu = Recipemenu::query()->first();
        $resp = $this->asEditor()->get($menu->getUrl('/create-book'));
        $resp->assertElementContains('form a[href="' . $menu->getUrl() . '"]', 'Cancel');
    }
}
