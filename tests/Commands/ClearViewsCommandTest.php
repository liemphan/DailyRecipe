<?php

namespace Tests\Commands;

use Artisan;
use DailyRecipe\Entities\Models\Page;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ClearViewsCommandTest extends TestCase
{
    public function test_clear_views_command()
    {
        $this->asEditor();
        $page = Page::first();

        $this->get($page->getUrl());

        $this->assertDatabaseHas('views', [
            'user_id'     => $this->getEditor()->id,
            'viewable_id' => $page->id,
            'views'       => 1,
        ]);

        DB::rollBack();
        $exitCode = Artisan::call('dailyrecipe:clear-views');
        DB::beginTransaction();
        $this->assertTrue($exitCode === 0, 'Command executed successfully');

        $this->assertDatabaseMissing('views', [
            'user_id' => $this->getEditor()->id,
        ]);
    }
}
