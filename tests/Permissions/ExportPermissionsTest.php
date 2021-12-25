<?php

namespace Tests\Permissions;

use DailyRecipe\Entities\Models\Recipe;
use DailyRecipe\Entities\Models\Chapter;
use Illuminate\Support\Str;
use Tests\TestCase;

class ExportPermissionsTest extends TestCase
{
    public function test_page_content_without_view_access_hidden_on_chapter_export()
    {
        $chapter = Chapter::query()->first();
        $page = $chapter->pages()->firstOrFail();
        $pageContent = Str::random(48);
        $page->html = '<p>' . $pageContent . '</p>';
        $page->save();
        $viewer = $this->getViewer();
        $this->actingAs($viewer);
        $formats = ['html', 'plaintext'];

        foreach ($formats as $format) {
            $resp = $this->get($chapter->getUrl("export/{$format}"));
            $resp->assertStatus(200);
            $resp->assertSee($page->name);
            $resp->assertSee($pageContent);
        }

        $this->setEntityRestrictions($page, []);

        foreach ($formats as $format) {
            $resp = $this->get($chapter->getUrl("export/{$format}"));
            $resp->assertStatus(200);
            $resp->assertDontSee($page->name);
            $resp->assertDontSee($pageContent);
        }
    }

    public function test_page_content_without_view_access_hidden_on_book_export()
    {
        $book = Recipe::query()->first();
        $page = $book->pages()->firstOrFail();
        $pageContent = Str::random(48);
        $page->html = '<p>' . $pageContent . '</p>';
        $page->save();
        $viewer = $this->getViewer();
        $this->actingAs($viewer);
        $formats = ['html', 'plaintext'];

        foreach ($formats as $format) {
            $resp = $this->get($book->getUrl("export/{$format}"));
            $resp->assertStatus(200);
            $resp->assertSee($page->name);
            $resp->assertSee($pageContent);
        }

        $this->setEntityRestrictions($page, []);

        foreach ($formats as $format) {
            $resp = $this->get($book->getUrl("export/{$format}"));
            $resp->assertStatus(200);
            $resp->assertDontSee($page->name);
            $resp->assertDontSee($pageContent);
        }
    }
}
