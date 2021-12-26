<?php

namespace Tests\Entity;

use DailyRecipe\Entities\Models\Recipe;
use DailyRecipe\Entities\Models\Chapter;
use Tests\TestCase;

class ChapterTest extends TestCase
{
    public function test_create()
    {
        /** @var Recipe $recipe */
        $recipe = Recipe::query()->first();

        $chapter = Chapter::factory()->make([
            'name' => 'My First Chapter',
        ]);

        $resp = $this->asEditor()->get($recipe->getUrl());
        $resp->assertElementContains('a[href="' . $recipe->getUrl('/create-chapter') . '"]', 'New Chapter');

        $resp = $this->get($recipe->getUrl('/create-chapter'));
        $resp->assertElementContains('form[action="' . $recipe->getUrl('/create-chapter') . '"][method="POST"]', 'Save Chapter');

        $resp = $this->post($recipe->getUrl('/create-chapter'), $chapter->only('name', 'description'));
        $resp->assertRedirect($recipe->getUrl('/chapter/my-first-chapter'));

        $resp = $this->get($recipe->getUrl('/chapter/my-first-chapter'));
        $resp->assertSee($chapter->name);
        $resp->assertSee($chapter->description);
    }

    public function test_delete()
    {
        $chapter = Chapter::query()->whereHas('pages')->first();
        $this->assertNull($chapter->deleted_at);
        $pageCount = $chapter->pages()->count();

        $deleteViewReq = $this->asEditor()->get($chapter->getUrl('/delete'));
        $deleteViewReq->assertSeeText('Are you sure you want to delete this chapter?');

        $deleteReq = $this->delete($chapter->getUrl());
        $deleteReq->assertRedirect($chapter->getParent()->getUrl());
        $this->assertActivityExists('chapter_delete', $chapter);

        $chapter->refresh();
        $this->assertNotNull($chapter->deleted_at);

        $this->assertTrue($chapter->pages()->count() === 0);
        $this->assertTrue($chapter->pages()->withTrashed()->count() === $pageCount);
        $this->assertTrue($chapter->deletions()->count() === 1);

        $redirectReq = $this->get($deleteReq->baseResponse->headers->get('location'));
        $redirectReq->assertNotificationContains('Chapter Successfully Deleted');
    }
}
