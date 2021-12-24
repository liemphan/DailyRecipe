<?php

namespace Tests\User;

use DailyRecipe\Entities\Models\Recipemenu;
use Tests\TestCase;

class UserPreferencesTest extends TestCase
{
    public function test_update_sort_preference()
    {
        $editor = $this->getEditor();
        $this->actingAs($editor);

        $updateRequest = $this->patch('/settings/users/' . $editor->id . '/change-sort/books', [
            'sort'  => 'created_at',
            'order' => 'desc',
        ]);
        $updateRequest->assertStatus(302);

        $this->assertDatabaseHas('settings', [
            'setting_key' => 'user:' . $editor->id . ':books_sort',
            'value'       => 'created_at',
        ]);
        $this->assertDatabaseHas('settings', [
            'setting_key' => 'user:' . $editor->id . ':books_sort_order',
            'value'       => 'desc',
        ]);
        $this->assertEquals('created_at', setting()->getForCurrentUser('recipes_sort'));
        $this->assertEquals('desc', setting()->getForCurrentUser('books_sort_order'));
    }

    public function test_update_sort_preference_defaults()
    {
        $editor = $this->getEditor();
        $this->actingAs($editor);

        $updateRequest = $this->patch('/settings/users/' . $editor->id . '/change-sort/recipemenus', [
            'sort'  => 'cat',
            'order' => 'dog',
        ]);
        $updateRequest->assertStatus(302);

        $this->assertEquals('name', setting()->getForCurrentUser('recipemenus_sort'));
        $this->assertEquals('asc', setting()->getForCurrentUser('recipemenus_sort_order'));
    }

    public function test_update_sort_bad_entity_type_handled()
    {
        $editor = $this->getEditor();
        $this->actingAs($editor);

        $updateRequest = $this->patch('/settings/users/' . $editor->id . '/change-sort/dogs', [
            'sort'  => 'name',
            'order' => 'asc',
        ]);
        $updateRequest->assertStatus(500);

        $this->assertNotEmpty('name', setting()->getForCurrentUser('recipemenus_sort'));
        $this->assertNotEmpty('asc', setting()->getForCurrentUser('recipemenus_sort_order'));
    }

    public function test_update_expansion_preference()
    {
        $editor = $this->getEditor();
        $this->actingAs($editor);

        $updateRequest = $this->patch('/settings/users/' . $editor->id . '/update-expansion-preference/home-details', ['expand' => 'true']);
        $updateRequest->assertStatus(204);

        $this->assertDatabaseHas('settings', [
            'setting_key' => 'user:' . $editor->id . ':section_expansion#home-details',
            'value'       => 'true',
        ]);
        $this->assertEquals(true, setting()->getForCurrentUser('section_expansion#home-details'));

        $invalidKeyRequest = $this->patch('/settings/users/' . $editor->id . '/update-expansion-preference/my-home-details', ['expand' => 'true']);
        $invalidKeyRequest->assertStatus(500);
    }

    public function test_toggle_dark_mode()
    {
        $home = $this->actingAs($this->getEditor())->get('/');
        $home->assertElementNotExists('.dark-mode');
        $home->assertSee('Dark Mode');

        $this->assertEquals(false, setting()->getForCurrentUser('dark-mode-enabled', false));
        $prefChange = $this->patch('/settings/users/toggle-dark-mode');
        $prefChange->assertRedirect();
        $this->assertEquals(true, setting()->getForCurrentUser('dark-mode-enabled'));

        $home = $this->actingAs($this->getEditor())->get('/');
        $home->assertElementExists('.dark-mode');
        $home->assertDontSee('Dark Mode');
        $home->assertSee('Light Mode');
    }

    public function test_dark_mode_defaults_to_config_option()
    {
        config()->set('setting-defaults.user.dark-mode-enabled', false);
        $this->assertEquals(false, setting()->getForCurrentUser('dark-mode-enabled'));
        $home = $this->get('/login');
        $home->assertElementNotExists('.dark-mode');

        config()->set('setting-defaults.user.dark-mode-enabled', true);
        $this->assertEquals(true, setting()->getForCurrentUser('dark-mode-enabled'));
        $home = $this->get('/login');
        $home->assertElementExists('.dark-mode');
    }

    public function test_books_view_type_preferences_when_list()
    {
        $editor = $this->getEditor();
        setting()->putUser($editor, 'books_view_type', 'list');

        $this->actingAs($editor)->get('/books')
            ->assertElementNotExists('.featured-image-container')
            ->assertElementExists('.content-wrap .entity-list-item');
    }

    public function test_books_view_type_preferences_when_grid()
    {
        $editor = $this->getEditor();
        setting()->putUser($editor, 'books_view_type', 'grid');

        $this->actingAs($editor)->get('/books')
            ->assertElementExists('.featured-image-container');
    }

    public function test_menu_view_type_change()
    {
        $editor = $this->getEditor();
        /** @var Recipemenu $menu */
        $menu = Recipemenu::query()->first();
        setting()->putUser($editor, 'recipemenu_view_type', 'list');

        $this->actingAs($editor)->get($menu->getUrl())
            ->assertElementNotExists('.featured-image-container')
            ->assertElementExists('.content-wrap .entity-list-item')
            ->assertSee('Grid View');

        $req = $this->patch("/settings/users/{$editor->id}/switch-menu-view", ['view_type' => 'grid']);
        $req->assertRedirect($menu->getUrl());

        $this->actingAs($editor)->get($menu->getUrl())
            ->assertElementExists('.featured-image-container')
            ->assertElementNotExists('.content-wrap .entity-list-item')
            ->assertSee('List View');
    }
}
