<?php

namespace Tests\Entity;

use DailyRecipe\Auth\UserRepo;
use DailyRecipe\Entities\Models\Entity;
use DailyRecipe\Entities\Repos\PageRepo;
use Tests\TestCase;

class EntityAccessTest extends TestCase
{
    public function test_entities_viewable_after_creator_deletion()
    {
        // Create required assets and revisions
        $creator = $this->getEditor();
        $updater = $this->getViewer();
        $entities = $this->createEntityChainBelongingToUser($creator, $updater);
        app()->make(UserRepo::class)->destroy($creator);
        app()->make(PageRepo::class)->update($entities['page'], ['html' => '<p>hello!</p>>']);

        $this->checkEntitiesViewable($entities);
    }

    public function test_entities_viewable_after_updater_deletion()
    {
        // Create required assets and revisions
        $creator = $this->getViewer();
        $updater = $this->getEditor();
        $entities = $this->createEntityChainBelongingToUser($creator, $updater);
        app()->make(UserRepo::class)->destroy($updater);
        app()->make(PageRepo::class)->update($entities['page'], ['html' => '<p>Hello there!</p>']);

        $this->checkEntitiesViewable($entities);
    }

    /**
     * @param array<string, Entity> $entities
     */
    private function checkEntitiesViewable(array $entities)
    {
        // Check pages and recipes are visible.
        $this->asAdmin();
        foreach ($entities as $entity) {
            $this->get($entity->getUrl())
                ->assertStatus(200)
                ->assertSee($entity->name);
        }

        // Check revision listing shows no errors.
        $this->get($entities['page']->getUrl('/revisions'))->assertStatus(200);
    }
}
