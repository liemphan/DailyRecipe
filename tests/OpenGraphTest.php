<?php

namespace Tests;

use DailyRecipe\Entities\Models\Book;
use DailyRecipe\Entities\Models\Recipemenu;
use DailyRecipe\Entities\Models\Chapter;
use DailyRecipe\Entities\Models\Page;
use DailyRecipe\Entities\Repos\BookRepo;
use DailyRecipe\Entities\Repos\RecipemenuRepo;
use Illuminate\Support\Str;
use Tests\Uploads\UsesImages;

class OpenGraphTest extends TestCase
{
    use UsesImages;

    public function test_page_tags()
    {
        $page = Page::query()->first();
        $resp = $this->asEditor()->get($page->getUrl());
        $tags = $this->getOpenGraphTags($resp);

        $this->assertEquals($page->getShortName() . ' | DailyRecipe', $tags['title']);
        $this->assertEquals($page->getUrl(), $tags['url']);
        $this->assertEquals(Str::limit($page->text, 100, '...'), $tags['description']);
    }

    public function test_chapter_tags()
    {
        $chapter = Chapter::query()->first();
        $resp = $this->asEditor()->get($chapter->getUrl());
        $tags = $this->getOpenGraphTags($resp);

        $this->assertEquals($chapter->getShortName() . ' | DailyRecipe', $tags['title']);
        $this->assertEquals($chapter->getUrl(), $tags['url']);
        $this->assertEquals(Str::limit($chapter->description, 100, '...'), $tags['description']);
    }

    public function test_book_tags()
    {
        $book = Book::query()->first();
        $resp = $this->asEditor()->get($book->getUrl());
        $tags = $this->getOpenGraphTags($resp);

        $this->assertEquals($book->getShortName() . ' | DailyRecipe', $tags['title']);
        $this->assertEquals($book->getUrl(), $tags['url']);
        $this->assertEquals(Str::limit($book->description, 100, '...'), $tags['description']);
        $this->assertArrayNotHasKey('image', $tags);

        // Test image set if image has cover image
        $bookRepo = app(BookRepo::class);
        $bookRepo->updateCoverImage($book, $this->getTestImage('image.png'));
        $resp = $this->asEditor()->get($book->getUrl());
        $tags = $this->getOpenGraphTags($resp);

        $this->assertEquals($book->getBookCover(), $tags['image']);
    }

    public function test_menu_tags()
    {
        $menu = Recipemenu::query()->first();
        $resp = $this->asEditor()->get($menu->getUrl());
        $tags = $this->getOpenGraphTags($resp);

        $this->assertEquals($menu->getShortName() . ' | DailyRecipe', $tags['title']);
        $this->assertEquals($menu->getUrl(), $tags['url']);
        $this->assertEquals(Str::limit($menu->description, 100, '...'), $tags['description']);
        $this->assertArrayNotHasKey('image', $tags);

        // Test image set if image has cover image
        $menuRepo = app(RecipemenuRepo::class);
        $menuRepo->updateCoverImage($menu, $this->getTestImage('image.png'));
        $resp = $this->asEditor()->get($menu->getUrl());
        $tags = $this->getOpenGraphTags($resp);

        $this->assertEquals($menu->getBookCover(), $tags['image']);
    }

    /**
     * Parse the open graph tags from a test response.
     */
    protected function getOpenGraphTags(TestResponse $resp): array
    {
        $tags = [];

        libxml_use_internal_errors(true);
        $doc = new \DOMDocument();
        $doc->loadHTML($resp->getContent());
        $metaElems = $doc->getElementsByTagName('meta');
        /** @var \DOMElement $elem */
        foreach ($metaElems as $elem) {
            $prop = $elem->getAttribute('property');
            $name = explode(':', $prop)[1] ?? null;
            if ($name) {
                $tags[$name] = $elem->getAttribute('content');
            }
        }

        return $tags;
    }
}
