<?php

namespace Tests\Commands;

use DailyRecipe\Entities\Models\Recipemenu;
use Tests\TestCase;

class CopyMenuPermissionsCommandTest extends TestCase
{
    public function test_copy_menu_permissions_command_shows_error_when_no_required_option_given()
    {
        $this->artisan('dailyrecipe:copy-menu-permissions')
            ->expectsOutput('Either a --slug or --all option must be provided.')
            ->assertExitCode(0);
    }

    public function test_copy_menu_permissions_command_using_slug()
    {
        $menu = Recipemenu::first();
        $child = $menu->books()->first();
        $editorRole = $this->getEditor()->roles()->first();
        $this->assertFalse(boolval($child->restricted), 'Child book should not be restricted by default');
        $this->assertTrue($child->permissions()->count() === 0, 'Child book should have no permissions by default');

        $this->setEntityRestrictions($menu, ['view', 'update'], [$editorRole]);
        $this->artisan('dailyrecipe:copy-menu-permissions', [
            '--slug' => $menu->slug,
        ]);
        $child = $menu->books()->first();

        $this->assertTrue(boolval($child->restricted), 'Child book should now be restricted');
        $this->assertTrue($child->permissions()->count() === 2, 'Child book should have copied permissions');
        $this->assertDatabaseHas('entity_permissions', ['restrictable_id' => $child->id, 'action' => 'view', 'role_id' => $editorRole->id]);
        $this->assertDatabaseHas('entity_permissions', ['restrictable_id' => $child->id, 'action' => 'update', 'role_id' => $editorRole->id]);
    }

    public function test_copy_menu_permissions_command_using_all()
    {
        $menu = Recipemenu::query()->first();
        Recipemenu::query()->where('id', '!=', $menu->id)->delete();
        $child = $menu->books()->first();
        $editorRole = $this->getEditor()->roles()->first();
        $this->assertFalse(boolval($child->restricted), 'Child book should not be restricted by default');
        $this->assertTrue($child->permissions()->count() === 0, 'Child book should have no permissions by default');

        $this->setEntityRestrictions($menu, ['view', 'update'], [$editorRole]);
        $this->artisan('dailyrecipe:copy-menu-permissions --all')
            ->expectsQuestion('Permission settings for all menus will be cascaded. Books assigned to multiple menus will receive only the permissions of it\'s last processed menu. Are you sure you want to proceed?', 'y');
        $child = $menu->books()->first();

        $this->assertTrue(boolval($child->restricted), 'Child book should now be restricted');
        $this->assertTrue($child->permissions()->count() === 2, 'Child book should have copied permissions');
        $this->assertDatabaseHas('entity_permissions', ['restrictable_id' => $child->id, 'action' => 'view', 'role_id' => $editorRole->id]);
        $this->assertDatabaseHas('entity_permissions', ['restrictable_id' => $child->id, 'action' => 'update', 'role_id' => $editorRole->id]);
    }
}
