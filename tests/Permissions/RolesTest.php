<?php

namespace Tests\Permissions;

use DailyRecipe\Actions\ActivityType;
use DailyRecipe\Actions\Comment;
use DailyRecipe\Auth\Role;
use DailyRecipe\Auth\User;
use DailyRecipe\Entities\Models\Recipe;
use DailyRecipe\Entities\Models\Recipemenu;
use DailyRecipe\Entities\Models\Chapter;
use DailyRecipe\Entities\Models\Entity;
use DailyRecipe\Entities\Models\Page;
use DailyRecipe\Uploads\Image;
use Tests\TestCase;
use Tests\TestResponse;

class RolesTest extends TestCase
{
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->getViewer();
    }

    public function test_admin_can_see_settings()
    {
        $this->asAdmin()->get('/settings')->assertSee('Settings');
    }

    public function test_cannot_delete_admin_role()
    {
        $adminRole = Role::getRole('admin');
        $deletePageUrl = '/settings/roles/delete/' . $adminRole->id;

        $this->asAdmin()->get($deletePageUrl);
        $this->delete($deletePageUrl)->assertRedirect($deletePageUrl);
        $this->get($deletePageUrl)->assertSee('cannot be deleted');
    }

    public function test_role_cannot_be_deleted_if_default()
    {
        $newRole = $this->createNewRole();
        $this->setSettings(['registration-role' => $newRole->id]);

        $deletePageUrl = '/settings/roles/delete/' . $newRole->id;
        $this->asAdmin()->get($deletePageUrl);
        $this->delete($deletePageUrl)->assertRedirect($deletePageUrl);
        $this->get($deletePageUrl)->assertSee('cannot be deleted');
    }

    public function test_role_create_update_delete_flow()
    {
        $testRoleName = 'Test Role';
        $testRoleDesc = 'a little test description';
        $testRoleUpdateName = 'An Super Updated role';

        // Creation
        $resp = $this->asAdmin()->get('/settings');
        $resp->assertElementContains('a[href="' . url('/settings/roles') . '"]', 'Roles');

        $resp = $this->get('/settings/roles');
        $resp->assertElementContains('a[href="' . url('/settings/roles/new') . '"]', 'Create New Role');

        $resp = $this->get('/settings/roles/new');
        $resp->assertElementContains('form[action="' . url('/settings/roles/new') . '"]', 'Save Role');

        $resp = $this->post('/settings/roles/new', [
            'display_name' => $testRoleName,
            'description'  => $testRoleDesc,
        ]);
        $resp->assertRedirect('/settings/roles');

        $resp = $this->get('/settings/roles');
        $resp->assertSee($testRoleName);
        $resp->assertSee($testRoleDesc);
        $this->assertDatabaseHas('roles', [
            'display_name' => $testRoleName,
            'description'  => $testRoleDesc,
            'mfa_enforced' => false,
        ]);

        /** @var Role $role */
        $role = Role::query()->where('display_name', '=', $testRoleName)->first();

        // Updating
        $resp = $this->get('/settings/roles/' . $role->id);
        $resp->assertSee($testRoleName);
        $resp->assertSee($testRoleDesc);
        $resp->assertElementContains('form[action="' . url('/settings/roles/' . $role->id) . '"]', 'Save Role');

        $resp = $this->put('/settings/roles/' . $role->id, [
            'display_name' => $testRoleUpdateName,
            'description'  => $testRoleDesc,
            'mfa_enforced' => 'true',
        ]);
        $resp->assertRedirect('/settings/roles');
        $this->assertDatabaseHas('roles', [
            'display_name' => $testRoleUpdateName,
            'description'  => $testRoleDesc,
            'mfa_enforced' => true,
        ]);

        // Deleting
        $resp = $this->get('/settings/roles/' . $role->id);
        $resp->assertElementContains('a[href="' . url("/settings/roles/delete/$role->id") . '"]', 'Delete Role');

        $resp = $this->get("/settings/roles/delete/$role->id");
        $resp->assertSee($testRoleUpdateName);
        $resp->assertElementContains('form[action="' . url("/settings/roles/delete/$role->id") . '"]', 'Confirm');

        $resp = $this->delete("/settings/roles/delete/$role->id");
        $resp->assertRedirect('/settings/roles');
        $this->get('/settings/roles')->assertSee('Role successfully deleted');
        $this->assertActivityExists(ActivityType::ROLE_DELETE);
    }

    public function test_admin_role_cannot_be_removed_if_user_last_admin()
    {
        /** @var Role $adminRole */
        $adminRole = Role::query()->where('system_name', '=', 'admin')->first();
        $adminUser = $this->getAdmin();
        $adminRole->users()->where('id', '!=', $adminUser->id)->delete();
        $this->assertEquals(1, $adminRole->users()->count());

        $viewerRole = $this->getViewer()->roles()->first();

        $editUrl = '/settings/users/' . $adminUser->id;
        $resp = $this->actingAs($adminUser)->put($editUrl, [
            'name'  => $adminUser->name,
            'email' => $adminUser->email,
            'roles' => [
                'viewer' => strval($viewerRole->id),
            ],
        ]);

        $resp->assertRedirect($editUrl);

        $resp = $this->get($editUrl);
        $resp->assertSee('This user is the only user assigned to the administrator role');
    }

    public function test_migrate_users_on_delete_works()
    {
        /** @var Role $roleA */
        $roleA = Role::query()->create(['display_name' => 'Delete Test A']);
        /** @var Role $roleB */
        $roleB = Role::query()->create(['display_name' => 'Delete Test B']);
        $this->user->attachRole($roleB);

        $this->assertCount(0, $roleA->users()->get());
        $this->assertCount(1, $roleB->users()->get());

        $deletePage = $this->asAdmin()->get("/settings/roles/delete/$roleB->id");
        $deletePage->assertElementExists('select[name=migrate_role_id]');
        $this->asAdmin()->delete("/settings/roles/delete/$roleB->id", [
            'migrate_role_id' => $roleA->id,
        ]);

        $this->assertCount(1, $roleA->users()->get());
        $this->assertEquals($this->user->id, $roleA->users()->first()->id);
    }

    public function test_manage_user_permission()
    {
        $this->actingAs($this->user)->get('/settings/users')->assertRedirect('/');
        $this->giveUserPermissions($this->user, ['users-manage']);
        $this->actingAs($this->user)->get('/settings/users')->assertOk();
    }

    public function test_manage_users_permission_shows_link_in_header_if_does_not_have_settings_manage_permision()
    {
        $usersLink = 'href="' . url('/settings/users') . '"';
        $this->actingAs($this->user)->get('/')->assertDontSee($usersLink, false);
        $this->giveUserPermissions($this->user, ['users-manage']);
        $this->actingAs($this->user)->get('/')->assertSee($usersLink, false);
        $this->giveUserPermissions($this->user, ['settings-manage', 'users-manage']);
        $this->actingAs($this->user)->get('/')->assertDontSee($usersLink, false);
    }

    public function test_user_cannot_change_email_unless_they_have_manage_users_permission()
    {
        $userProfileUrl = '/settings/users/' . $this->user->id;
        $originalEmail = $this->user->email;
        $this->actingAs($this->user);

        $this->get($userProfileUrl)
            ->assertOk()
            ->assertElementExists('input[name=email][disabled]');
        $this->put($userProfileUrl, [
            'name'  => 'my_new_name',
            'email' => 'new_email@example.com',
        ]);
        $this->assertDatabaseHas('users', [
            'id'    => $this->user->id,
            'email' => $originalEmail,
            'name'  => 'my_new_name',
        ]);

        $this->giveUserPermissions($this->user, ['users-manage']);

        $this->get($userProfileUrl)
            ->assertOk()
            ->assertElementNotExists('input[name=email][disabled]')
            ->assertElementExists('input[name=email]');
        $this->put($userProfileUrl, [
            'name'  => 'my_new_name_2',
            'email' => 'new_email@example.com',
        ]);

        $this->assertDatabaseHas('users', [
            'id'    => $this->user->id,
            'email' => 'new_email@example.com',
            'name'  => 'my_new_name_2',
        ]);
    }

    public function test_user_roles_manage_permission()
    {
        $this->actingAs($this->user)->get('/settings/roles')->assertRedirect('/');
        $this->get('/settings/roles/1')->assertRedirect('/');
        $this->giveUserPermissions($this->user, ['user-roles-manage']);
        $this->actingAs($this->user)->get('/settings/roles')->assertOk();
        $this->get('/settings/roles/1')
            ->assertOk()
            ->assertSee('Admin');
    }

    public function test_settings_manage_permission()
    {
        $this->actingAs($this->user)->get('/settings')->assertRedirect('/');
        $this->giveUserPermissions($this->user, ['settings-manage']);
        $this->get('/settings')->assertOk();

        $resp = $this->post('/settings', []);
        $resp->assertRedirect('/settings');
        $resp = $this->get('/settings');
        $resp->assertSee('Settings saved');
    }

    public function test_restrictions_manage_all_permission()
    {
        $page = Page::query()->get()->first();

        $this->actingAs($this->user)->get($page->getUrl())->assertDontSee('Permissions');
        $this->get($page->getUrl('/permissions'))->assertRedirect('/');

        $this->giveUserPermissions($this->user, ['restrictions-manage-all']);

        $this->actingAs($this->user)->get($page->getUrl())->assertSee('Permissions');

        $this->get($page->getUrl('/permissions'))
            ->assertOk()
            ->assertSee('Page Permissions');
    }

    public function test_restrictions_manage_own_permission()
    {
        /** @var Page $otherUsersPage */
        $otherUsersPage = Page::query()->first();
        $content = $this->createEntityChainBelongingToUser($this->user);

        // Set a different creator on the page we're checking to ensure
        // that the owner fields are checked
        $page = $content['page']; /** @var Page $page */
        $page->created_by = $otherUsersPage->id;
        $page->owned_by = $this->user->id;
        $page->save();

        // Check can't restrict other's content
        $this->actingAs($this->user)->get($otherUsersPage->getUrl())->assertDontSee('Permissions');
        $this->get($otherUsersPage->getUrl('/permissions'))->assertRedirect('/');

        // Check can't restrict own content
        $this->actingAs($this->user)->get($page->getUrl())->assertDontSee('Permissions');
        $this->get($page->getUrl('/permissions'))->assertRedirect('/');

        $this->giveUserPermissions($this->user, ['restrictions-manage-own']);

        // Check can't restrict other's content
        $this->actingAs($this->user)->get($otherUsersPage->getUrl())->assertDontSee('Permissions');
        $this->get($otherUsersPage->getUrl('/permissions'))->assertRedirect();

        // Check can restrict own content
        $this->actingAs($this->user)->get($page->getUrl())->assertSee('Permissions');
        $this->get($page->getUrl('/permissions'))->assertOk();
    }

    /**
     * Check a standard entity access permission.
     */
    private function checkAccessPermission(string $permission, array $accessUrls = [], array $visibles = [])
    {
        foreach ($accessUrls as $url) {
            $this->actingAs($this->user)->get($url)->assertRedirect('/');
        }

        foreach ($visibles as $url => $text) {
            $this->actingAs($this->user)->get($url)
                ->assertElementNotContains('.action-buttons', $text);
        }

        $this->giveUserPermissions($this->user, [$permission]);

        foreach ($accessUrls as $url) {
            $this->actingAs($this->user)->get($url)->assertOk();
        }
        foreach ($visibles as $url => $text) {
            $this->actingAs($this->user)->get($url)->assertSee($text);
        }
    }

    public function test_recipemenus_create_all_permissions()
    {
        $this->checkAccessPermission('recipemenu-create-all', [
            '/create-menu',
        ], [
            '/menus' => 'New Menu',
        ]);

        $this->post('/menus', [
            'name'        => 'test menu',
            'description' => 'menu desc',
        ])->assertRedirect('/menus/test-menu');
    }

    public function test_recipemenus_edit_own_permission()
    {
        /** @var Recipemenu $otherMenu */
        $otherMenu = Recipemenu::query()->first();
        $ownMenu = $this->newMenu(['name' => 'test-menu', 'slug' => 'test-menu']);
        $ownMenu->forceFill(['owned_by' => $this->user->id, 'updated_by' => $this->user->id])->save();
        $this->regenEntityPermissions($ownMenu);

        $this->checkAccessPermission('recipemenu-update-own', [
            $ownMenu->getUrl('/edit'),
        ], [
            $ownMenu->getUrl() => 'Edit',
        ]);

        $this->get($otherMenu->getUrl())->assertElementNotContains('.action-buttons', 'Edit');
        $this->get($otherMenu->getUrl('/edit'))->assertRedirect('/');
    }

    public function test_recipemenus_edit_all_permission()
    {
        /** @var Recipemenu $otherMenu */
        $otherMenu = Recipemenu::query()->first();
        $this->checkAccessPermission('recipemenu-update-all', [
            $otherMenu->getUrl('/edit'),
        ], [
            $otherMenu->getUrl() => 'Edit',
        ]);
    }

    public function test_recipemenus_delete_own_permission()
    {
        $this->giveUserPermissions($this->user, ['recipemenu-update-all']);
        /** @var Recipemenu $otherMenu */
        $otherMenu = Recipemenu::query()->first();
        $ownMenu = $this->newMenu(['name' => 'test-menu', 'slug' => 'test-menu']);
        $ownMenu->forceFill(['owned_by' => $this->user->id, 'updated_by' => $this->user->id])->save();
        $this->regenEntityPermissions($ownMenu);

        $this->checkAccessPermission('recipemenu-delete-own', [
            $ownMenu->getUrl('/delete'),
        ], [
            $ownMenu->getUrl() => 'Delete',
        ]);

        $this->get($otherMenu->getUrl())->assertElementNotContains('.action-buttons', 'Delete');
        $this->get($otherMenu->getUrl('/delete'))->assertRedirect('/');

        $this->get($ownMenu->getUrl());
        $this->delete($ownMenu->getUrl())->assertRedirect('/menus');
        $this->get('/menus')->assertDontSee($ownMenu->name);
    }

    public function test_recipemenus_delete_all_permission()
    {
        $this->giveUserPermissions($this->user, ['recipemenu-update-all']);
        /** @var Recipemenu $otherMenu */
        $otherMenu = Recipemenu::query()->first();
        $this->checkAccessPermission('recipemenu-delete-all', [
            $otherMenu->getUrl('/delete'),
        ], [
            $otherMenu->getUrl() => 'Delete',
        ]);

        $this->delete($otherMenu->getUrl())->assertRedirect('/menus');
        $this->get('/menus')->assertDontSee($otherMenu->name);
    }

    public function test_recipes_create_all_permissions()
    {
        $this->checkAccessPermission('recipe-create-all', [
            '/create-recipe',
        ], [
            '/recipes' => 'Create New Recipe',
        ]);

        $this->post('/recipes', [
            'name'        => 'test recipe',
            'description' => 'recipe desc',
        ])->assertRedirect('/recipes/test-recipe');
    }

    public function test_recipes_edit_own_permission()
    {
        /** @var Recipe $otherRecipe */
        $otherRecipe = Recipe::query()->take(1)->get()->first();
        $ownRecipe = $this->createEntityChainBelongingToUser($this->user)['recipe'];
        $this->checkAccessPermission('recipe-update-own', [
            $ownRecipe->getUrl() . '/edit',
        ], [
            $ownRecipe->getUrl() => 'Edit',
        ]);

        $this->get($otherRecipe->getUrl())->assertElementNotContains('.action-buttons', 'Edit');
        $this->get($otherRecipe->getUrl('/edit'))->assertRedirect('/');
    }

    public function test_recipes_edit_all_permission()
    {
        /** @var Recipe $otherRecipe */
        $otherRecipe = Recipe::query()->take(1)->get()->first();
        $this->checkAccessPermission('recipe-update-all', [
            $otherRecipe->getUrl() . '/edit',
        ], [
            $otherRecipe->getUrl() => 'Edit',
        ]);
    }

    public function test_recipes_delete_own_permission()
    {
        $this->giveUserPermissions($this->user, ['recipe-update-all']);
        /** @var Recipe $otherRecipe */
        $otherRecipe = Recipe::query()->take(1)->get()->first();
        $ownRecipe = $this->createEntityChainBelongingToUser($this->user)['recipe'];
        $this->checkAccessPermission('recipe-delete-own', [
            $ownRecipe->getUrl() . '/delete',
        ], [
            $ownRecipe->getUrl() => 'Delete',
        ]);

        $this->get($otherRecipe->getUrl())->assertElementNotContains('.action-buttons', 'Delete');
        $this->get($otherRecipe->getUrl('/delete'))->assertRedirect('/');
        $this->get($ownRecipe->getUrl());
        $this->delete($ownRecipe->getUrl())->assertRedirect('/recipes');
        $this->get('/recipes')->assertDontSee($ownRecipe->name);
    }

    public function test_recipes_delete_all_permission()
    {
        $this->giveUserPermissions($this->user, ['recipe-update-all']);
        /** @var Recipe $otherRecipe */
        $otherRecipe = Recipe::query()->take(1)->get()->first();
        $this->checkAccessPermission('recipe-delete-all', [
            $otherRecipe->getUrl() . '/delete',
        ], [
            $otherRecipe->getUrl() => 'Delete',
        ]);

        $this->get($otherRecipe->getUrl());
        $this->delete($otherRecipe->getUrl())->assertRedirect('/recipes');
        $this->get('/recipes')->assertDontSee($otherRecipe->name);
    }

    public function test_chapter_create_own_permissions()
    {
        /** @var Recipe $recipe */
        $recipe = Recipe::query()->take(1)->get()->first();
        $ownRecipe = $this->createEntityChainBelongingToUser($this->user)['recipe'];
        $this->checkAccessPermission('chapter-create-own', [
            $ownRecipe->getUrl('/create-chapter'),
        ], [
            $ownRecipe->getUrl() => 'New Chapter',
        ]);

        $this->post($ownRecipe->getUrl('/create-chapter'), [
            'name'        => 'test chapter',
            'description' => 'chapter desc',
        ])->assertRedirect($ownRecipe->getUrl('/chapter/test-chapter'));

        $this->get($recipe->getUrl())->assertElementNotContains('.action-buttons', 'New Chapter');
        $this->get($recipe->getUrl('/create-chapter'))->assertRedirect('/');
    }

    public function test_chapter_create_all_permissions()
    {
        /** @var Recipe $recipe */
        $recipe = Recipe::query()->first();
        $this->checkAccessPermission('chapter-create-all', [
            $recipe->getUrl('/create-chapter'),
        ], [
            $recipe->getUrl() => 'New Chapter',
        ]);

        $this->post($recipe->getUrl('/create-chapter'), [
            'name'        => 'test chapter',
            'description' => 'chapter desc',
        ])->assertRedirect($recipe->getUrl('/chapter/test-chapter'));
    }

    public function test_chapter_edit_own_permission()
    {
        /** @var Chapter $otherChapter */
        $otherChapter = Chapter::query()->first();
        $ownChapter = $this->createEntityChainBelongingToUser($this->user)['chapter'];
        $this->checkAccessPermission('chapter-update-own', [
            $ownChapter->getUrl() . '/edit',
        ], [
            $ownChapter->getUrl() => 'Edit',
        ]);

        $this->get($otherChapter->getUrl())->assertElementNotContains('.action-buttons', 'Edit');
        $this->get($otherChapter->getUrl('/edit'))->assertRedirect('/');
    }

    public function test_chapter_edit_all_permission()
    {
        /** @var Chapter $otherChapter */
        $otherChapter = Chapter::query()->take(1)->get()->first();
        $this->checkAccessPermission('chapter-update-all', [
            $otherChapter->getUrl() . '/edit',
        ], [
            $otherChapter->getUrl() => 'Edit',
        ]);
    }

    public function test_chapter_delete_own_permission()
    {
        $this->giveUserPermissions($this->user, ['chapter-update-all']);
        /** @var Chapter $otherChapter */
        $otherChapter = Chapter::query()->first();
        $ownChapter = $this->createEntityChainBelongingToUser($this->user)['chapter'];
        $this->checkAccessPermission('chapter-delete-own', [
            $ownChapter->getUrl() . '/delete',
        ], [
            $ownChapter->getUrl() => 'Delete',
        ]);

        $recipeUrl = $ownChapter->recipe->getUrl();
        $this->get($otherChapter->getUrl())->assertElementNotContains('.action-buttons', 'Delete');
        $this->get($otherChapter->getUrl('/delete'))->assertRedirect('/');
        $this->get($ownChapter->getUrl());
        $this->delete($ownChapter->getUrl())->assertRedirect($recipeUrl);
        $this->get($recipeUrl)->assertElementNotContains('.recipe-content', $ownChapter->name);
    }

    public function test_chapter_delete_all_permission()
    {
        $this->giveUserPermissions($this->user, ['chapter-update-all']);
        /** @var Chapter $otherChapter */
        $otherChapter = Chapter::query()->first();
        $this->checkAccessPermission('chapter-delete-all', [
            $otherChapter->getUrl() . '/delete',
        ], [
            $otherChapter->getUrl() => 'Delete',
        ]);

        $recipeUrl = $otherChapter->recipe->getUrl();
        $this->get($otherChapter->getUrl());
        $this->delete($otherChapter->getUrl())->assertRedirect($recipeUrl);
        $this->get($recipeUrl)->assertElementNotContains('.recipe-content', $otherChapter->name);
    }

    public function test_page_create_own_permissions()
    {
        /** @var Recipe $recipe */
        $recipe = Recipe::query()->first();
        /** @var Chapter $chapter */
        $chapter = Chapter::query()->first();

        $entities = $this->createEntityChainBelongingToUser($this->user);
        $ownRecipe = $entities['recipe'];
        $ownChapter = $entities['chapter'];

        $createUrl = $ownRecipe->getUrl('/create-page');
        $createUrlChapter = $ownChapter->getUrl('/create-page');
        $accessUrls = [$createUrl, $createUrlChapter];

        foreach ($accessUrls as $url) {
            $this->actingAs($this->user)->get($url)->assertRedirect('/');
        }

        $this->checkAccessPermission('page-create-own', [], [
            $ownRecipe->getUrl()    => 'New Page',
            $ownChapter->getUrl() => 'New Page',
        ]);

        $this->giveUserPermissions($this->user, ['page-create-own']);

        foreach ($accessUrls as $index => $url) {
            $resp = $this->actingAs($this->user)->get($url);
            $expectedUrl = Page::query()->where('draft', '=', true)->orderBy('id', 'desc')->first()->getUrl();
            $resp->assertRedirect($expectedUrl);
        }

        $this->get($createUrl);
        /** @var Page $draft */
        $draft = Page::query()->where('draft', '=', true)->orderBy('id', 'desc')->first();
        $this->post($draft->getUrl(), [
            'name' => 'test page',
            'html' => 'page desc',
        ])->assertRedirect($ownRecipe->getUrl('/page/test-page'));

        $this->get($recipe->getUrl())->assertElementNotContains('.action-buttons', 'New Page');
        $this->get($recipe->getUrl('/create-page'))->assertRedirect('/');

        $this->get($chapter->getUrl())->assertElementNotContains('.action-buttons', 'New Page');
        $this->get($chapter->getUrl('/create-page'))->assertRedirect('/');
    }

    public function test_page_create_all_permissions()
    {
        /** @var Recipe $recipe */
        $recipe = Recipe::query()->first();
        /** @var Chapter $chapter */
        $chapter = Chapter::query()->first();
        $createUrl = $recipe->getUrl('/create-page');

        $createUrlChapter = $chapter->getUrl('/create-page');
        $accessUrls = [$createUrl, $createUrlChapter];

        foreach ($accessUrls as $url) {
            $this->actingAs($this->user)->get($url)->assertRedirect('/');
        }

        $this->checkAccessPermission('page-create-all', [], [
            $recipe->getUrl()    => 'New Page',
            $chapter->getUrl() => 'New Page',
        ]);

        $this->giveUserPermissions($this->user, ['page-create-all']);

        foreach ($accessUrls as $index => $url) {
            $resp = $this->actingAs($this->user)->get($url);
            $expectedUrl = Page::query()->where('draft', '=', true)->orderBy('id', 'desc')->first()->getUrl();
            $resp->assertRedirect($expectedUrl);
        }

        $this->get($createUrl);
        /** @var Page $draft */
        $draft = Page::query()->where('draft', '=', true)->orderByDesc('id')->first();
        $this->post($draft->getUrl(), [
            'name' => 'test page',
            'html' => 'page desc',
        ])->assertRedirect($recipe->getUrl('/page/test-page'));

        $this->get($chapter->getUrl('/create-page'));
        /** @var Page $draft */
        $draft = Page::query()->where('draft', '=', true)->orderByDesc('id')->first();
        $this->post($draft->getUrl(), [
            'name' => 'new test page',
            'html' => 'page desc',
        ])->assertRedirect($recipe->getUrl('/page/new-test-page'));
    }

    public function test_page_edit_own_permission()
    {
        /** @var Page $otherPage */
        $otherPage = Page::query()->first();
        $ownPage = $this->createEntityChainBelongingToUser($this->user)['page'];
        $this->checkAccessPermission('page-update-own', [
            $ownPage->getUrl() . '/edit',
        ], [
            $ownPage->getUrl() => 'Edit',
        ]);

        $this->get($otherPage->getUrl())->assertElementNotContains('.action-buttons', 'Edit');
        $this->get($otherPage->getUrl() . '/edit')->assertRedirect('/');
    }

    public function test_page_edit_all_permission()
    {
        /** @var Page $otherPage */
        $otherPage = Page::query()->first();
        $this->checkAccessPermission('page-update-all', [
            $otherPage->getUrl('/edit'),
        ], [
            $otherPage->getUrl() => 'Edit',
        ]);
    }

    public function test_page_delete_own_permission()
    {
        $this->giveUserPermissions($this->user, ['page-update-all']);
        /** @var Page $otherPage */
        $otherPage = Page::query()->first();
        $ownPage = $this->createEntityChainBelongingToUser($this->user)['page'];
        $this->checkAccessPermission('page-delete-own', [
            $ownPage->getUrl() . '/delete',
        ], [
            $ownPage->getUrl() => 'Delete',
        ]);

        $parent = $ownPage->chapter ?? $ownPage->recipe;
        $this->get($otherPage->getUrl())->assertElementNotContains('.action-buttons', 'Delete');
        $this->get($otherPage->getUrl('/delete'))->assertRedirect('/');
        $this->get($ownPage->getUrl());
        $this->delete($ownPage->getUrl())->assertRedirect($parent->getUrl());
        $this->get($parent->getUrl())->assertElementNotContains('.recipe-content', $ownPage->name);
    }

    public function test_page_delete_all_permission()
    {
        $this->giveUserPermissions($this->user, ['page-update-all']);
        /** @var Page $otherPage */
        $otherPage = Page::query()->first();

        $this->checkAccessPermission('page-delete-all', [
            $otherPage->getUrl() . '/delete',
        ], [
            $otherPage->getUrl() => 'Delete',
        ]);

        /** @var Entity $parent */
        $parent = $otherPage->chapter ?? $otherPage->recipe;
        $this->get($otherPage->getUrl());

        $this->delete($otherPage->getUrl())->assertRedirect($parent->getUrl());
        $this->get($parent->getUrl())->assertDontSee($otherPage->name);
    }

    public function test_public_role_visible_in_user_edit_screen()
    {
        /** @var User $user */
        $user = User::query()->first();
        $adminRole = Role::getSystemRole('admin');
        $publicRole = Role::getSystemRole('public');
        $this->asAdmin()->get('/settings/users/' . $user->id)
            ->assertElementExists('[name="roles[' . $adminRole->id . ']"]')
            ->assertElementExists('[name="roles[' . $publicRole->id . ']"]');
    }

    public function test_public_role_visible_in_role_listing()
    {
        $this->asAdmin()->get('/settings/roles')
            ->assertSee('Admin')
            ->assertSee('Public');
    }

    public function test_public_role_visible_in_default_role_setting()
    {
        $this->asAdmin()->get('/settings')
            ->assertElementExists('[data-system-role-name="admin"]')
            ->assertElementExists('[data-system-role-name="public"]');
    }

    public function test_public_role_not_deletable()
    {
        /** @var Role $publicRole */
        $publicRole = Role::getSystemRole('public');
        $resp = $this->asAdmin()->delete('/settings/roles/delete/' . $publicRole->id);
        $resp->assertRedirect('/');

        $this->get('/settings/roles/delete/' . $publicRole->id);
        $resp = $this->delete('/settings/roles/delete/' . $publicRole->id);
        $resp->assertRedirect('/settings/roles/delete/' . $publicRole->id);
        $resp = $this->get('/settings/roles/delete/' . $publicRole->id);
        $resp->assertSee('This role is a system role and cannot be deleted');
    }

    public function test_image_delete_own_permission()
    {
        $this->giveUserPermissions($this->user, ['image-update-all']);
        /** @var Page $page */
        $page = Page::query()->first();
        $image = Image::factory()->create([
            'uploaded_to' => $page->id,
            'created_by'  => $this->user->id,
            'updated_by'  => $this->user->id,
        ]);

        $this->actingAs($this->user)->json('delete', '/images/' . $image->id)->assertStatus(403);

        $this->giveUserPermissions($this->user, ['image-delete-own']);

        $this->actingAs($this->user)->json('delete', '/images/' . $image->id)->assertOk();
        $this->assertDatabaseMissing('images', ['id' => $image->id]);
    }

    public function test_image_delete_all_permission()
    {
        $this->giveUserPermissions($this->user, ['image-update-all']);
        $admin = $this->getAdmin();
        /** @var Page $page */
        $page = Page::query()->first();
        $image = Image::factory()->create(['uploaded_to' => $page->id, 'created_by' => $admin->id, 'updated_by' => $admin->id]);

        $this->actingAs($this->user)->json('delete', '/images/' . $image->id)->assertStatus(403);

        $this->giveUserPermissions($this->user, ['image-delete-own']);

        $this->actingAs($this->user)->json('delete', '/images/' . $image->id)->assertStatus(403);

        $this->giveUserPermissions($this->user, ['image-delete-all']);

        $this->actingAs($this->user)->json('delete', '/images/' . $image->id)->assertOk();
        $this->assertDatabaseMissing('images', ['id' => $image->id]);
    }

    public function test_role_permission_removal()
    {
        // To cover issue fixed in f99c8ff99aee9beb8c692f36d4b84dc6e651e50a.
        /** @var Page $page */
        $page = Page::query()->first();
        $viewerRole = Role::getRole('viewer');
        $viewer = $this->getViewer();
        $this->actingAs($viewer)->get($page->getUrl())->assertOk();

        $this->asAdmin()->put('/settings/roles/' . $viewerRole->id, [
            'display_name' => $viewerRole->display_name,
            'description'  => $viewerRole->description,
            'permission'   => [],
        ])->assertStatus(302);

        $this->actingAs($viewer)->get($page->getUrl())->assertStatus(404);
    }

    public function test_empty_state_actions_not_visible_without_permission()
    {
        $admin = $this->getAdmin();
        // Recipe links
        $recipe = Recipe::factory()->create(['created_by' => $admin->id, 'updated_by' => $admin->id]);
        $this->regenEntityPermissions($recipe);
        $this->actingAs($this->getViewer())->get($recipe->getUrl())
            ->assertDontSee('Create a new page')
            ->assertDontSee('Add a chapter');

        // Chapter links
        $chapter = Chapter::factory()->create(['created_by' => $admin->id, 'updated_by' => $admin->id, 'recipe_id' => $recipe->id]);
        $this->regenEntityPermissions($chapter);
        $this->actingAs($this->getViewer())->get($chapter->getUrl())
            ->assertDontSee('Create a new page')
            ->assertDontSee('Sort the current recipe');
    }

    public function test_comment_create_permission()
    {
        $ownPage = $this->createEntityChainBelongingToUser($this->user)['page'];

        $this->actingAs($this->user)
            ->addComment($ownPage)
            ->assertStatus(403);

        $this->giveUserPermissions($this->user, ['comment-create-all']);

        $this->actingAs($this->user)
            ->addComment($ownPage)
            ->assertOk();
    }

    public function test_comment_update_own_permission()
    {
        $ownPage = $this->createEntityChainBelongingToUser($this->user)['page'];
        $this->giveUserPermissions($this->user, ['comment-create-all']);
        $this->actingAs($this->user)->addComment($ownPage);
        /** @var Comment $comment */
        $comment = $ownPage->comments()->latest()->first();

        // no comment-update-own
        $this->actingAs($this->user)->updateComment($comment)->assertStatus(403);

        $this->giveUserPermissions($this->user, ['comment-update-own']);

        // now has comment-update-own
        $this->actingAs($this->user)->updateComment($comment)->assertOk();
    }

    public function test_comment_update_all_permission()
    {
        /** @var Page $ownPage */
        $ownPage = $this->createEntityChainBelongingToUser($this->user)['page'];
        $this->asAdmin()->addComment($ownPage);
        /** @var Comment $comment */
        $comment = $ownPage->comments()->latest()->first();

        // no comment-update-all
        $this->actingAs($this->user)->updateComment($comment)->assertStatus(403);

        $this->giveUserPermissions($this->user, ['comment-update-all']);

        // now has comment-update-all
        $this->actingAs($this->user)->updateComment($comment)->assertOk();
    }

    public function test_comment_delete_own_permission()
    {
        /** @var Page $ownPage */
        $ownPage = $this->createEntityChainBelongingToUser($this->user)['page'];
        $this->giveUserPermissions($this->user, ['comment-create-all']);
        $this->actingAs($this->user)->addComment($ownPage);

        /** @var Comment $comment */
        $comment = $ownPage->comments()->latest()->first();

        // no comment-delete-own
        $this->actingAs($this->user)->deleteComment($comment)->assertStatus(403);

        $this->giveUserPermissions($this->user, ['comment-delete-own']);

        // now has comment-update-own
        $this->actingAs($this->user)->deleteComment($comment)->assertOk();
    }

    public function test_comment_delete_all_permission()
    {
        /** @var Page $ownPage */
        $ownPage = $this->createEntityChainBelongingToUser($this->user)['page'];
        $this->asAdmin()->addComment($ownPage);
        /** @var Comment $comment */
        $comment = $ownPage->comments()->latest()->first();

        // no comment-delete-all
        $this->actingAs($this->user)->deleteComment($comment)->assertStatus(403);

        $this->giveUserPermissions($this->user, ['comment-delete-all']);

        // now has comment-delete-all
        $this->actingAs($this->user)->deleteComment($comment)->assertOk();
    }

    private function addComment(Page $page): TestResponse
    {
        $comment = Comment::factory()->make();

        return $this->postJson("/comment/$page->id", $comment->only('text', 'html'));
    }

    private function updateComment(Comment $comment): TestResponse
    {
        $commentData = Comment::factory()->make();

        return $this->putJson("/comment/{$comment->id}", $commentData->only('text', 'html'));
    }

    private function deleteComment(Comment $comment): TestResponse
    {
        return $this->json('DELETE', '/comment/' . $comment->id);
    }
}
