<?php

namespace Tests;

use DailyRecipe\Auth\Role;
use DailyRecipe\Auth\User;
use DailyRecipe\Entities\Models\Recipemenu;
use DailyRecipe\Entities\Models\Page;

class HomepageTest extends TestCase
{
    public function test_default_homepage_visible()
    {
        $this->asEditor();
        $homeVisit = $this->get('/');
        $homeVisit->assertSee('My Recently Viewed');
        $homeVisit->assertSee('Recently Updated Pages');
        $homeVisit->assertSee('Recent Activity');
        $homeVisit->assertSee('home-default');
    }

    public function test_custom_homepage()
    {
        $this->asEditor();
        $name = 'My custom homepage';
        $content = str_repeat('This is the body content of my custom homepage.', 20);
        $customPage = $this->newPage(['name' => $name, 'html' => $content]);
        $this->setSettings(['app-homepage' => $customPage->id]);
        $this->setSettings(['app-homepage-type' => 'page']);

        $homeVisit = $this->get('/');
        $homeVisit->assertSee($name);
        $homeVisit->assertSee($content);
        $homeVisit->assertSee('My Recently Viewed');
        $homeVisit->assertSee('Recently Updated Pages');
        $homeVisit->assertSee('Recent Activity');
    }

    public function test_delete_custom_homepage()
    {
        $this->asEditor();
        $name = 'My custom homepage';
        $content = str_repeat('This is the body content of my custom homepage.', 20);
        $customPage = $this->newPage(['name' => $name, 'html' => $content]);
        $this->setSettings([
            'app-homepage'      => $customPage->id,
            'app-homepage-type' => 'page',
        ]);

        $homeVisit = $this->get('/');
        $homeVisit->assertSee($name);
        $homeVisit->assertElementNotExists('#home-default');

        $pageDeleteReq = $this->delete($customPage->getUrl());
        $pageDeleteReq->assertStatus(302);
        $pageDeleteReq->assertRedirect($customPage->getUrl());
        $pageDeleteReq->assertSessionHas('error');
        $pageDeleteReq->assertSessionMissing('success');

        $homeVisit = $this->get('/');
        $homeVisit->assertSee($name);
        $homeVisit->assertStatus(200);
    }

    public function test_custom_homepage_can_be_deleted_once_custom_homepage_no_longer_used()
    {
        $this->asEditor();
        $name = 'My custom homepage';
        $content = str_repeat('This is the body content of my custom homepage.', 20);
        $customPage = $this->newPage(['name' => $name, 'html' => $content]);
        $this->setSettings([
            'app-homepage'      => $customPage->id,
            'app-homepage-type' => 'default',
        ]);

        $pageDeleteReq = $this->delete($customPage->getUrl());
        $pageDeleteReq->assertStatus(302);
        $pageDeleteReq->assertSessionHas('success');
        $pageDeleteReq->assertSessionMissing('error');
    }

    public function test_custom_homepage_renders_includes()
    {
        $this->asEditor();
        /** @var Page $included */
        $included = Page::query()->first();
        $content = str_repeat('This is the body content of my custom homepage.', 20);
        $included->html = $content;
        $included->save();

        $name = 'My custom homepage';
        $customPage = $this->newPage(['name' => $name, 'html' => '{{@' . $included->id . '}}']);
        $this->setSettings(['app-homepage' => $customPage->id]);
        $this->setSettings(['app-homepage-type' => 'page']);

        $homeVisit = $this->get('/');
        $homeVisit->assertSee($name);
        $homeVisit->assertSee($content);
    }

    public function test_set_recipe_homepage()
    {
        $editor = $this->getEditor();
        setting()->putUser($editor, 'recipes_view_type', 'grid');

        $this->setSettings(['app-homepage-type' => 'recipes']);

        $this->asEditor();
        $homeVisit = $this->get('/');
        $homeVisit->assertSee('Recipes');
        $homeVisit->assertSee('grid-card');
        $homeVisit->assertSee('grid-card-content');
        $homeVisit->assertSee('grid-card-footer');
        $homeVisit->assertSee('featured-image-container');

        $this->setSettings(['app-homepage-type' => false]);
        $this->test_default_homepage_visible();
    }

    public function test_set_recipemenus_homepage()
    {
        $editor = $this->getEditor();
        setting()->putUser($editor, 'recipemenus_view_type', 'grid');
        $menu = Recipemenu::query()->firstOrFail();

        $this->setSettings(['app-homepage-type' => 'recipemenus']);

        $this->asEditor();
        $homeVisit = $this->get('/');
        $homeVisit->assertSee('Menus');
        $homeVisit->assertSee('grid-card-content');
        $homeVisit->assertSee('featured-image-container');
        $homeVisit->assertElementContains('.grid-card', $menu->name);

        $this->setSettings(['app-homepage-type' => false]);
        $this->test_default_homepage_visible();
    }

    public function test_menus_list_homepage_adheres_to_recipe_visibility_permissions()
    {
        $editor = $this->getEditor();
        setting()->putUser($editor, 'recipemenus_view_type', 'list');
        $this->setSettings(['app-homepage-type' => 'recipemenus']);
        $this->asEditor();

        $menu = Recipemenu::query()->first();
        $recipe = $menu->recipes()->first();

        // Ensure initially visible
        $homeVisit = $this->get('/');
        $homeVisit->assertElementContains('.content-wrap', $menu->name);
        $homeVisit->assertElementContains('.content-wrap', $recipe->name);

        // Ensure recipe no longer visible without view permission
        $editor->roles()->detach();
        $this->giveUserPermissions($editor, ['recipemenu-view-all']);
        $homeVisit = $this->get('/');
        $homeVisit->assertElementContains('.content-wrap', $menu->name);
        $homeVisit->assertElementNotContains('.content-wrap', $recipe->name);

        // Ensure is visible again with entity-level view permission
        $this->setEntityRestrictions($recipe, ['view'], [$editor->roles()->first()]);
        $homeVisit = $this->get('/');
        $homeVisit->assertElementContains('.content-wrap', $menu->name);
        $homeVisit->assertElementContains('.content-wrap', $recipe->name);
    }

    public function test_new_users_dont_have_any_recently_viewed()
    {
        $user = User::factory()->create();
        $viewRole = Role::getRole('Viewer');
        $user->attachRole($viewRole);

        $homeVisit = $this->actingAs($user)->get('/');
        $homeVisit->assertElementContains('#recently-viewed', 'You have not viewed any pages');
    }
}
