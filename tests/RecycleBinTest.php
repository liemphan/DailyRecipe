<?php

namespace Tests;

use DailyRecipe\Entities\Models\Recipe;
use DailyRecipe\Entities\Models\Recipemenu;
use DailyRecipe\Entities\Models\Chapter;
use DailyRecipe\Entities\Models\Deletion;
use DailyRecipe\Entities\Models\Entity;
use DailyRecipe\Entities\Models\Page;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class RecycleBinTest extends TestCase
{
    public function test_recycle_bin_routes_permissions()
    {
        $page = Page::query()->first();
        $editor = $this->getEditor();
        $this->actingAs($editor)->delete($page->getUrl());
        $deletion = Deletion::query()->firstOrFail();

        $routes = [
            'GET:/settings/recycle-bin',
            'POST:/settings/recycle-bin/empty',
            "GET:/settings/recycle-bin/{$deletion->id}/destroy",
            "GET:/settings/recycle-bin/{$deletion->id}/restore",
            "POST:/settings/recycle-bin/{$deletion->id}/restore",
            "DELETE:/settings/recycle-bin/{$deletion->id}",
        ];

        foreach ($routes as $route) {
            [$method, $url] = explode(':', $route);
            $resp = $this->call($method, $url);
            $this->assertPermissionError($resp);
        }

        $this->giveUserPermissions($editor, ['restrictions-manage-all']);

        foreach ($routes as $route) {
            [$method, $url] = explode(':', $route);
            $resp = $this->call($method, $url);
            $this->assertPermissionError($resp);
        }

        $this->giveUserPermissions($editor, ['settings-manage']);

        foreach ($routes as $route) {
            DB::beginTransaction();
            [$method, $url] = explode(':', $route);
            $resp = $this->call($method, $url);
            $this->assertNotPermissionError($resp);
            DB::rollBack();
        }
    }

    public function test_recycle_bin_view()
    {
        $page = Page::query()->first();
        $recipe = Recipe::query()->whereHas('pages')->whereHas('chapters')->withCount(['pages', 'chapters'])->first();
        $editor = $this->getEditor();
        $this->actingAs($editor)->delete($page->getUrl());
        $this->actingAs($editor)->delete($recipe->getUrlContent());

        $viewReq = $this->asAdmin()->get('/settings/recycle-bin');
        $viewReq->assertElementContains('table.table', $page->name);
        $viewReq->assertElementContains('table.table', $editor->name);
        $viewReq->assertElementContains('table.table', $recipe->name);
        $viewReq->assertElementContains('table.table', $recipe->pages_count . ' Pages');
        $viewReq->assertElementContains('table.table', $recipe->chapters_count . ' Chapters');
    }

    public function test_recycle_bin_empty()
    {
        $page = Page::query()->first();
        $recipe = Recipe::query()->where('id', '!=', $page->recipe_id)->whereHas('pages')->whereHas('chapters')->with(['pages', 'chapters'])->firstOrFail();
        $editor = $this->getEditor();
        $this->actingAs($editor)->delete($page->getUrl());
        $this->actingAs($editor)->delete($recipe->getUrlContent());

        $this->assertTrue(Deletion::query()->count() === 2);
        $emptyReq = $this->asAdmin()->post('/settings/recycle-bin/empty');
        $emptyReq->assertRedirect('/settings/recycle-bin');

        $this->assertTrue(Deletion::query()->count() === 0);
        $this->assertDatabaseMissing('recipes', ['id' => $recipe->id]);
        $this->assertDatabaseMissing('pages', ['id' => $page->id]);
        $this->assertDatabaseMissing('pages', ['id' => $recipe->pages->first()->id]);
        $this->assertDatabaseMissing('chapters', ['id' => $recipe->chapters->first()->id]);

        $itemCount = 2 + $recipe->pages->count() + $recipe->chapters->count();
        $redirectReq = $this->get('/settings/recycle-bin');
        $redirectReq->assertNotificationContains('Deleted ' . $itemCount . ' total items from the recycle bin');
    }

    public function test_entity_restore()
    {
        $recipe = Recipe::query()->whereHas('pages')->whereHas('chapters')->with(['pages', 'chapters'])->firstOrFail();
        $this->asEditor()->delete($recipe->getUrlContent());
        $deletion = Deletion::query()->firstOrFail();

        $this->assertEquals($recipe->pages->count(), DB::table('pages')->where('recipe_id', '=', $recipe->id)->whereNotNull('deleted_at')->count());
        $this->assertEquals($recipe->chapters->count(), DB::table('chapters')->where('recipe_id', '=', $recipe->id)->whereNotNull('deleted_at')->count());

        $restoreReq = $this->asAdmin()->post("/settings/recycle-bin/{$deletion->id}/restore");
        $restoreReq->assertRedirect('/settings/recycle-bin');
        $this->assertTrue(Deletion::query()->count() === 0);

        $this->assertEquals($recipe->pages->count(), DB::table('pages')->where('recipe_id', '=', $recipe->id)->whereNull('deleted_at')->count());
        $this->assertEquals($recipe->chapters->count(), DB::table('chapters')->where('recipe_id', '=', $recipe->id)->whereNull('deleted_at')->count());

        $itemCount = 1 + $recipe->pages->count() + $recipe->chapters->count();
        $redirectReq = $this->get('/settings/recycle-bin');
        $redirectReq->assertNotificationContains('Restored ' . $itemCount . ' total items from the recycle bin');
    }

    public function test_permanent_delete()
    {
        $recipe = Recipe::query()->whereHas('pages')->whereHas('chapters')->with(['pages', 'chapters'])->firstOrFail();
        $this->asEditor()->delete($recipe->getUrlContent());
        $deletion = Deletion::query()->firstOrFail();

        $deleteReq = $this->asAdmin()->delete("/settings/recycle-bin/{$deletion->id}");
        $deleteReq->assertRedirect('/settings/recycle-bin');
        $this->assertTrue(Deletion::query()->count() === 0);

        $this->assertDatabaseMissing('recipes', ['id' => $recipe->id]);
        $this->assertDatabaseMissing('pages', ['id' => $recipe->pages->first()->id]);
        $this->assertDatabaseMissing('chapters', ['id' => $recipe->chapters->first()->id]);

        $itemCount = 1 + $recipe->pages->count() + $recipe->chapters->count();
        $redirectReq = $this->get('/settings/recycle-bin');
        $redirectReq->assertNotificationContains('Deleted ' . $itemCount . ' total items from the recycle bin');
    }

    public function test_permanent_delete_for_each_type()
    {
        /** @var Entity $entity */
        foreach ([new Recipemenu(), new Recipe(), new Chapter(), new Page()] as $entity) {
            $entity = $entity->newQuery()->first();
            $this->asEditor()->delete($entity->getUrlContent());
            $deletion = Deletion::query()->orderBy('id', 'desc')->firstOrFail();

            $deleteReq = $this->asAdmin()->delete("/settings/recycle-bin/{$deletion->id}");
            $deleteReq->assertRedirect('/settings/recycle-bin');
            $this->assertDatabaseMissing('deletions', ['id' => $deletion->id]);
            $this->assertDatabaseMissing($entity->getTable(), ['id' => $entity->id]);
        }
    }

    public function test_permanent_entity_delete_updates_existing_activity_with_entity_name()
    {
        $page = Page::query()->firstOrFail();
        $this->asEditor()->delete($page->getUrl());
        $deletion = $page->deletions()->firstOrFail();

        $this->assertDatabaseHas('activities', [
            'type'        => 'page_delete',
            'entity_id'   => $page->id,
            'entity_type' => $page->getMorphClass(),
        ]);

        $this->asAdmin()->delete("/settings/recycle-bin/{$deletion->id}");

        $this->assertDatabaseMissing('activities', [
            'type'        => 'page_delete',
            'entity_id'   => $page->id,
            'entity_type' => $page->getMorphClass(),
        ]);

        $this->assertDatabaseHas('activities', [
            'type'        => 'page_delete',
            'entity_id'   => null,
            'entity_type' => null,
            'detail'      => $page->name,
        ]);
    }

    public function test_auto_clear_functionality_works()
    {
        config()->set('app.recycle_bin_lifetime', 5);
        $page = Page::query()->firstOrFail();
        $otherPage = Page::query()->where('id', '!=', $page->id)->firstOrFail();

        $this->asEditor()->delete($page->getUrl());
        $this->assertDatabaseHas('pages', ['id' => $page->id]);
        $this->assertEquals(1, Deletion::query()->count());

        Carbon::setTestNow(Carbon::now()->addDays(6));
        $this->asEditor()->delete($otherPage->getUrl());
        $this->assertEquals(1, Deletion::query()->count());

        $this->assertDatabaseMissing('pages', ['id' => $page->id]);
    }

    public function test_auto_clear_functionality_with_negative_time_keeps_forever()
    {
        config()->set('app.recycle_bin_lifetime', -1);
        $page = Page::query()->firstOrFail();
        $otherPage = Page::query()->where('id', '!=', $page->id)->firstOrFail();

        $this->asEditor()->delete($page->getUrl());
        $this->assertEquals(1, Deletion::query()->count());

        Carbon::setTestNow(Carbon::now()->addDays(6000));
        $this->asEditor()->delete($otherPage->getUrl());
        $this->assertEquals(2, Deletion::query()->count());

        $this->assertDatabaseHas('pages', ['id' => $page->id]);
    }

    public function test_auto_clear_functionality_with_zero_time_deletes_instantly()
    {
        config()->set('app.recycle_bin_lifetime', 0);
        $page = Page::query()->firstOrFail();

        $this->asEditor()->delete($page->getUrl());
        $this->assertDatabaseMissing('pages', ['id' => $page->id]);
        $this->assertEquals(0, Deletion::query()->count());
    }

    public function test_restore_flow_when_restoring_nested_delete_first()
    {
        $recipe = Recipe::query()->whereHas('pages')->whereHas('chapters')->with(['pages', 'chapters'])->firstOrFail();
        $chapter = $recipe->chapters->first();
        $this->asEditor()->delete($chapter->getUrlContent());
        $this->asEditor()->delete($recipe->getUrlContent());

        $recipeDeletion = $recipe->deletions()->first();
        $chapterDeletion = $chapter->deletions()->first();

        $chapterRestoreView = $this->asAdmin()->get("/settings/recycle-bin/{$chapterDeletion->id}/restore");
        $chapterRestoreView->assertStatus(200);
        $chapterRestoreView->assertSeeText($chapter->name);

        $chapterRestore = $this->post("/settings/recycle-bin/{$chapterDeletion->id}/restore");
        $chapterRestore->assertRedirect('/settings/recycle-bin');
        $this->assertDatabaseMissing('deletions', ['id' => $chapterDeletion->id]);

        $chapter->refresh();
        $this->assertNotNull($chapter->deleted_at);

        $recipeRestoreView = $this->asAdmin()->get("/settings/recycle-bin/{$recipeDeletion->id}/restore");
        $recipeRestoreView->assertStatus(200);
        $recipeRestoreView->assertSeeText($chapter->name);

        $this->post("/settings/recycle-bin/{$recipeDeletion->id}/restore");
        $chapter->refresh();
        $this->assertNull($chapter->deleted_at);
    }

    public function test_restore_page_shows_link_to_parent_restore_if_parent_also_deleted()
    {
        /** @var Recipe $recipe */
        $recipe = Recipe::query()->whereHas('pages')->whereHas('chapters')->with(['pages', 'chapters'])->firstOrFail();
        $chapter = $recipe->chapters->first();
        /** @var Page $page */
        $page = $chapter->pages->first();
        $this->asEditor()->delete($page->getUrl());
        $this->asEditor()->delete($recipe->getUrl());

        $recipeDeletion = $recipe->deletions()->first();
        $pageDeletion = $page->deletions()->first();

        $pageRestoreView = $this->asAdmin()->get("/settings/recycle-bin/{$pageDeletion->id}/restore");
        $pageRestoreView->assertSee('The parent of this item has also been deleted.');
        $pageRestoreView->assertElementContains('a[href$="/settings/recycle-bin/' . $recipeDeletion->id . '/restore"]', 'Restore Parent');
    }
}
