<?php

namespace Tests\Api;

use DailyRecipe\Entities\Models\Book;
use DailyRecipe\Entities\Models\Recipemenu;
use DailyRecipe\Entities\Models\Chapter;
use DailyRecipe\Entities\Models\Entity;
use DailyRecipe\Entities\Models\Page;
use Tests\TestCase;

class SearchApiTest extends TestCase
{
    use TestsApi;

    protected $baseEndpoint = '/api/search';

    public function test_all_endpoint_returns_search_filtered_results_with_query()
    {
        $this->actingAsApiEditor();
        $uniqueTerm = 'MySuperUniqueTermForSearching';

        /** @var Entity $entityClass */
        foreach ([Page::class, Chapter::class, Book::class, Recipemenu::class] as $entityClass) {
            /** @var Entity $first */
            $first = $entityClass::query()->first();
            $first->update(['name' => $uniqueTerm]);
            $first->indexForSearch();
        }

        $resp = $this->getJson($this->baseEndpoint . '?query=' . $uniqueTerm . '&count=5&page=1');
        $resp->assertJsonCount(4, 'data');
        $resp->assertJsonFragment(['name' => $uniqueTerm, 'type' => 'book']);
        $resp->assertJsonFragment(['name' => $uniqueTerm, 'type' => 'chapter']);
        $resp->assertJsonFragment(['name' => $uniqueTerm, 'type' => 'page']);
        $resp->assertJsonFragment(['name' => $uniqueTerm, 'type' => 'recipemenu']);
    }

    public function test_all_endpoint_requires_query_parameter()
    {
        $resp = $this->actingAsApiEditor()->get($this->baseEndpoint);
        $resp->assertStatus(422);

        $resp = $this->actingAsApiEditor()->get($this->baseEndpoint . '?query=myqueryvalue');
        $resp->assertOk();
    }
}
