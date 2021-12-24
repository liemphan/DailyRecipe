<?php

namespace Tests\Permissions;

use DailyRecipe\Auth\User;
use DailyRecipe\Entities\Models\Book;
use DailyRecipe\Entities\Models\Recipemenu;
use DailyRecipe\Entities\Models\Chapter;
use DailyRecipe\Entities\Models\Page;
use Tests\TestCase;

class EntityOwnerChangeTest extends TestCase
{
    public function test_changing_page_owner()
    {
        $page = Page::query()->first();
        $user = User::query()->where('id', '!=', $page->owned_by)->first();

        $this->asAdmin()->put($page->getUrl('permissions'), ['owned_by' => $user->id]);
        $this->assertDatabaseHas('pages', ['owned_by' => $user->id, 'id' => $page->id]);
    }

    public function test_changing_chapter_owner()
    {
        $chapter = Chapter::query()->first();
        $user = User::query()->where('id', '!=', $chapter->owned_by)->first();

        $this->asAdmin()->put($chapter->getUrl('permissions'), ['owned_by' => $user->id]);
        $this->assertDatabaseHas('chapters', ['owned_by' => $user->id, 'id' => $chapter->id]);
    }

    public function test_changing_book_owner()
    {
        $book = Book::query()->first();
        $user = User::query()->where('id', '!=', $book->owned_by)->first();

        $this->asAdmin()->put($book->getUrl('permissions'), ['owned_by' => $user->id]);
        $this->assertDatabaseHas('books', ['owned_by' => $user->id, 'id' => $book->id]);
    }

    public function test_changing_menu_owner()
    {
        $menu = Recipemenu::query()->first();
        $user = User::query()->where('id', '!=', $menu->owned_by)->first();

        $this->asAdmin()->put($menu->getUrl('permissions'), ['owned_by' => $user->id]);
        $this->assertDatabaseHas('recipemenus', ['owned_by' => $user->id, 'id' => $menu->id]);
    }
}
