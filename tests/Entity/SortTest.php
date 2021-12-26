<?php

namespace Tests\Entity;

use DailyRecipe\Entities\Models\Recipe;
use DailyRecipe\Entities\Models\Chapter;
use DailyRecipe\Entities\Models\Page;
use DailyRecipe\Entities\Repos\PageRepo;
use Tests\TestCase;

class SortTest extends TestCase
{
    protected $recipe;

    protected function setUp(): void
    {
        parent::setUp();
        $this->recipe = Recipe::first();
    }

    public function test_drafts_do_not_show_up()
    {
        $this->asAdmin();
        $pageRepo = app(PageRepo::class);
        $draft = $pageRepo->getNewDraftPage($this->recipe);

        $resp = $this->get($this->recipe->getUrl());
        $resp->assertSee($draft->name);

        $resp = $this->get($this->recipe->getUrl() . '/sort');
        $resp->assertDontSee($draft->name);
    }

    public function test_page_move_into_recipe()
    {
        $page = Page::first();
        $currentRecipe = $page->recipe;
        $newRecipe = Recipe::where('id', '!=', $currentRecipe->id)->first();

        $resp = $this->asEditor()->get($page->getUrl('/move'));
        $resp->assertSee('Move Page');

        $movePageResp = $this->put($page->getUrl('/move'), [
            'entity_selection' => 'recipe:' . $newRecipe->id,
        ]);
        $page = Page::find($page->id);

        $movePageResp->assertRedirect($page->getUrl());
        $this->assertTrue($page->recipe->id == $newRecipe->id, 'Page recipe is now the new recipe');

        $newRecipeResp = $this->get($newRecipe->getUrl());
        $newRecipeResp->assertSee('moved page');
        $newRecipeResp->assertSee($page->name);
    }

    public function test_page_move_into_chapter()
    {
        $page = Page::first();
        $currentRecipe = $page->recipe;
        $newRecipe = Recipe::where('id', '!=', $currentRecipe->id)->first();
        $newChapter = $newRecipe->chapters()->first();

        $movePageResp = $this->actingAs($this->getEditor())->put($page->getUrl('/move'), [
            'entity_selection' => 'chapter:' . $newChapter->id,
        ]);
        $page = Page::find($page->id);

        $movePageResp->assertRedirect($page->getUrl());
        $this->assertTrue($page->recipe->id == $newRecipe->id, 'Page parent is now the new chapter');

        $newChapterResp = $this->get($newChapter->getUrl());
        $newChapterResp->assertSee($page->name);
    }

    public function test_page_move_from_chapter_to_recipe()
    {
        $oldChapter = Chapter::first();
        $page = $oldChapter->pages()->first();
        $newRecipe = Recipe::where('id', '!=', $oldChapter->recipe_id)->first();

        $movePageResp = $this->actingAs($this->getEditor())->put($page->getUrl('/move'), [
            'entity_selection' => 'recipe:' . $newRecipe->id,
        ]);
        $page->refresh();

        $movePageResp->assertRedirect($page->getUrl());
        $this->assertTrue($page->recipe->id == $newRecipe->id, 'Page parent is now the new recipe');
        $this->assertTrue($page->chapter === null, 'Page has no parent chapter');

        $newRecipeResp = $this->get($newRecipe->getUrl());
        $newRecipeResp->assertSee($page->name);
    }

    public function test_page_move_requires_create_permissions_on_parent()
    {
        $page = Page::query()->first();
        $currentRecipe = $page->recipe;
        $newRecipe = Recipe::query()->where('id', '!=', $currentRecipe->id)->first();
        $editor = $this->getEditor();

        $this->setEntityRestrictions($newRecipe, ['view', 'update', 'delete'], $editor->roles->all());

        $movePageResp = $this->actingAs($editor)->put($page->getUrl('/move'), [
            'entity_selection' => 'recipe:' . $newRecipe->id,
        ]);
        $this->assertPermissionError($movePageResp);

        $this->setEntityRestrictions($newRecipe, ['view', 'update', 'delete', 'create'], $editor->roles->all());
        $movePageResp = $this->put($page->getUrl('/move'), [
            'entity_selection' => 'recipe:' . $newRecipe->id,
        ]);

        $page = Page::find($page->id);
        $movePageResp->assertRedirect($page->getUrl());

        $this->assertTrue($page->recipe->id == $newRecipe->id, 'Page recipe is now the new recipe');
    }

    public function test_page_move_requires_delete_permissions()
    {
        $page = Page::first();
        $currentRecipe = $page->recipe;
        $newRecipe = Recipe::where('id', '!=', $currentRecipe->id)->first();
        $editor = $this->getEditor();

        $this->setEntityRestrictions($newRecipe, ['view', 'update', 'create', 'delete'], $editor->roles->all());
        $this->setEntityRestrictions($page, ['view', 'update', 'create'], $editor->roles->all());

        $movePageResp = $this->actingAs($editor)->put($page->getUrl('/move'), [
            'entity_selection' => 'recipe:' . $newRecipe->id,
        ]);
        $this->assertPermissionError($movePageResp);
        $pageView = $this->get($page->getUrl());
        $pageView->assertDontSee($page->getUrl('/move'));

        $this->setEntityRestrictions($page, ['view', 'update', 'create', 'delete'], $editor->roles->all());
        $movePageResp = $this->put($page->getUrl('/move'), [
            'entity_selection' => 'recipe:' . $newRecipe->id,
        ]);

        $page = Page::find($page->id);
        $movePageResp->assertRedirect($page->getUrl());
        $this->assertTrue($page->recipe->id == $newRecipe->id, 'Page recipe is now the new recipe');
    }

    public function test_chapter_move()
    {
        $chapter = Chapter::first();
        $currentRecipe = $chapter->recipe;
        $pageToCheck = $chapter->pages->first();
        $newRecipe = Recipe::where('id', '!=', $currentRecipe->id)->first();

        $chapterMoveResp = $this->asEditor()->get($chapter->getUrl('/move'));
        $chapterMoveResp->assertSee('Move Chapter');

        $moveChapterResp = $this->put($chapter->getUrl('/move'), [
            'entity_selection' => 'recipe:' . $newRecipe->id,
        ]);

        $chapter = Chapter::find($chapter->id);
        $moveChapterResp->assertRedirect($chapter->getUrl());
        $this->assertTrue($chapter->recipe->id === $newRecipe->id, 'Chapter Recipe is now the new recipe');

        $newRecipeResp = $this->get($newRecipe->getUrl());
        $newRecipeResp->assertSee('moved chapter');
        $newRecipeResp->assertSee($chapter->name);

        $pageToCheck = Page::find($pageToCheck->id);
        $this->assertTrue($pageToCheck->recipe_id === $newRecipe->id, 'Chapter child page\'s recipe id has changed to the new recipe');
        $pageCheckResp = $this->get($pageToCheck->getUrl());
        $pageCheckResp->assertSee($newRecipe->name);
    }

    public function test_chapter_move_requires_delete_permissions()
    {
        $chapter = Chapter::first();
        $currentRecipe = $chapter->recipe;
        $newRecipe = Recipe::where('id', '!=', $currentRecipe->id)->first();
        $editor = $this->getEditor();

        $this->setEntityRestrictions($newRecipe, ['view', 'update', 'create', 'delete'], $editor->roles->all());
        $this->setEntityRestrictions($chapter, ['view', 'update', 'create'], $editor->roles->all());

        $moveChapterResp = $this->actingAs($editor)->put($chapter->getUrl('/move'), [
            'entity_selection' => 'recipe:' . $newRecipe->id,
        ]);
        $this->assertPermissionError($moveChapterResp);
        $pageView = $this->get($chapter->getUrl());
        $pageView->assertDontSee($chapter->getUrl('/move'));

        $this->setEntityRestrictions($chapter, ['view', 'update', 'create', 'delete'], $editor->roles->all());
        $moveChapterResp = $this->put($chapter->getUrl('/move'), [
            'entity_selection' => 'recipe:' . $newRecipe->id,
        ]);

        $chapter = Chapter::find($chapter->id);
        $moveChapterResp->assertRedirect($chapter->getUrl());
        $this->assertTrue($chapter->recipe->id == $newRecipe->id, 'Page recipe is now the new recipe');
    }

    public function test_chapter_move_changes_recipe_for_deleted_pages_within()
    {
        /** @var Chapter $chapter */
        $chapter = Chapter::query()->whereHas('pages')->first();
        $currentRecipe = $chapter->recipe;
        $pageToCheck = $chapter->pages->first();
        $newRecipe = Recipe::query()->where('id', '!=', $currentRecipe->id)->first();

        $pageToCheck->delete();

        $this->asEditor()->put($chapter->getUrl('/move'), [
            'entity_selection' => 'recipe:' . $newRecipe->id,
        ]);

        $pageToCheck->refresh();
        $this->assertEquals($newRecipe->id, $pageToCheck->recipe_id);
    }

    public function test_recipe_sort_page_shows()
    {
        /** @var Recipe $recipeToSort */
        $recipeToSort = Recipe::query()->first();

        $resp = $this->asAdmin()->get($recipeToSort->getUrl());
        $resp->assertElementExists('a[href="' . $recipeToSort->getUrl('/sort') . '"]');

        $resp = $this->get($recipeToSort->getUrl('/sort'));
        $resp->assertStatus(200);
        $resp->assertSee($recipeToSort->name);
    }

    public function test_recipe_sort()
    {
        $oldRecipe = Recipe::query()->first();
        $chapterToMove = $this->newChapter(['name' => 'chapter to move'], $oldRecipe);
        $newRecipe = $this->newRecipe(['name' => 'New sort recipe']);
        $pagesToMove = Page::query()->take(5)->get();

        // Create request data
        $reqData = [
            [
                'id'            => $chapterToMove->id,
                'sort'          => 0,
                'parentChapter' => false,
                'type'          => 'chapter',
                'recipe'          => $newRecipe->id,
            ],
        ];
        foreach ($pagesToMove as $index => $page) {
            $reqData[] = [
                'id'            => $page->id,
                'sort'          => $index,
                'parentChapter' => $index === count($pagesToMove) - 1 ? $chapterToMove->id : false,
                'type'          => 'page',
                'recipe'          => $newRecipe->id,
            ];
        }

        $sortResp = $this->asEditor()->put($newRecipe->getUrl() . '/sort', ['sort-tree' => json_encode($reqData)]);
        $sortResp->assertRedirect($newRecipe->getUrl());
        $sortResp->assertStatus(302);
        $this->assertDatabaseHas('chapters', [
            'id'       => $chapterToMove->id,
            'recipe_id'  => $newRecipe->id,
            'priority' => 0,
        ]);
        $this->assertTrue($newRecipe->chapters()->count() === 1);
        $this->assertTrue($newRecipe->chapters()->first()->pages()->count() === 1);

        $checkPage = $pagesToMove[1];
        $checkResp = $this->get(Page::find($checkPage->id)->getUrl());
        $checkResp->assertSee($newRecipe->name);
    }

    public function test_recipe_sort_item_returns_recipe_content()
    {
        $recipes = Recipe::all();
        $recipeToSort = $recipes[0];
        $firstPage = $recipeToSort->pages[0];
        $firstChapter = $recipeToSort->chapters[0];

        $resp = $this->asAdmin()->get($recipeToSort->getUrl() . '/sort-item');

        // Ensure recipe details are returned
        $resp->assertSee($recipeToSort->name);
        $resp->assertSee($firstPage->name);
        $resp->assertSee($firstChapter->name);
    }

    public function test_pages_in_recipe_show_sorted_by_priority()
    {
        /** @var Recipe $recipe */
        $recipe = Recipe::query()->whereHas('pages')->first();
        $recipe->chapters()->forceDelete();
        /** @var Page[] $pages */
        $pages = $recipe->pages()->where('chapter_id', '=', 0)->take(2)->get();
        $recipe->pages()->whereNotIn('id', $pages->pluck('id'))->delete();

        $resp = $this->asEditor()->get($recipe->getUrl());
        $resp->assertElementContains('.content-wrap a.page:nth-child(1)', $pages[0]->name);
        $resp->assertElementContains('.content-wrap a.page:nth-child(2)', $pages[1]->name);

        $pages[0]->forceFill(['priority' => 10])->save();
        $pages[1]->forceFill(['priority' => 5])->save();

        $resp = $this->asEditor()->get($recipe->getUrl());
        $resp->assertElementContains('.content-wrap a.page:nth-child(1)', $pages[1]->name);
        $resp->assertElementContains('.content-wrap a.page:nth-child(2)', $pages[0]->name);
    }
}
