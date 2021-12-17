<?php

namespace Tests\Commands;

use DailyRecipe\Auth\Permissions\JointPermission;
use DailyRecipe\Entities\Models\Page;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RegeneratePermissionsCommandTest extends TestCase
{
    public function test_regen_permissions_command()
    {
        \DB::rollBack();
        JointPermission::query()->truncate();
        $page = Page::first();

        $this->assertDatabaseMissing('joint_permissions', ['entity_id' => $page->id]);

        $exitCode = \Artisan::call('dailyrecipe:regenerate-permissions');
        $this->assertTrue($exitCode === 0, 'Command executed successfully');
        DB::beginTransaction();

        $this->assertDatabaseHas('joint_permissions', ['entity_id' => $page->id]);
    }
}
