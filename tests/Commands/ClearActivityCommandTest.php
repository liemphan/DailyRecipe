<?php

namespace Tests\Commands;

use Activity;
use Artisan;
use DailyRecipe\Actions\ActivityType;
use DailyRecipe\Entities\Models\Page;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ClearActivityCommandTest extends TestCase
{
    public function test_clear_activity_command()
    {
        $this->asEditor();
        $page = Page::first();
        Activity::addForEntity($page, ActivityType::PAGE_UPDATE);

        $this->assertDatabaseHas('activities', [
            'type'      => 'page_update',
            'entity_id' => $page->id,
            'user_id'   => $this->getEditor()->id,
        ]);

        DB::rollBack();
        $exitCode = Artisan::call('dailyrecipe:clear-activity');
        DB::beginTransaction();
        $this->assertTrue($exitCode === 0, 'Command executed successfully');

        $this->assertDatabaseMissing('activities', [
            'type' => 'page_update',
        ]);
    }
}
