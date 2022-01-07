<?php

namespace Tests\Api;

use DailyRecipe\Entities\Models\Recipe;
use Tests\TestCase;

class ApiListingTest extends TestCase
{
    use TestsApi;

    protected $endpoint = '/api/recipes';

    public function test_count_parameter_limits_responses()
    {
        $this->actingAsApiEditor();
        $recipeCount = min(Recipe::visible()->count(), 100);

        $resp = $this->get($this->endpoint);
        $resp->assertJsonCount($recipeCount, 'data');

        $resp = $this->get($this->endpoint . '?count=1');
        $resp->assertJsonCount(1, 'data');
    }

    public function test_offset_parameter()
    {
        $this->actingAsApiEditor();
        $recipes = Recipe::visible()->orderBy('id')->take(3)->get();

        $resp = $this->get($this->endpoint . '?count=1');
        $resp->assertJsonMissing(['name' => $recipes[1]->name]);

        $resp = $this->get($this->endpoint . '?count=1&offset=1000');
        $resp->assertJsonCount(0, 'data');
    }

    public function test_sort_parameter()
    {
        $this->actingAsApiEditor();

        $sortChecks = [
            '-id' => Recipe::visible()->orderBy('id', 'desc')->first(),
            '+name' => Recipe::visible()->orderBy('name', 'asc')->first(),
            'name' => Recipe::visible()->orderBy('name', 'asc')->first(),
            '-name' => Recipe::visible()->orderBy('name', 'desc')->first(),
        ];

        foreach ($sortChecks as $sortOption => $result) {
            $resp = $this->get($this->endpoint . '?count=1&sort=' . $sortOption);
            $resp->assertJson(['data' => [
                [
                    'id' => $result->id,
                    'name' => $result->name,
                ],
            ]]);
        }
    }

    public function test_filter_parameter()
    {
        $this->actingAsApiEditor();
        $recipe = Recipe::visible()->first();
        $nameSubstr = substr($recipe->name, 0, 4);
        $encodedNameSubstr = rawurlencode($nameSubstr);

        $filterChecks = [
            // Test different types of filter
            "filter[id]={$recipe->id}" => 1,
            "filter[id:ne]={$recipe->id}" => Recipe::visible()->where('id', '!=', $recipe->id)->count(),
            "filter[id:gt]={$recipe->id}" => Recipe::visible()->where('id', '>', $recipe->id)->count(),
            "filter[id:gte]={$recipe->id}" => Recipe::visible()->where('id', '>=', $recipe->id)->count(),
            "filter[id:lt]={$recipe->id}" => Recipe::visible()->where('id', '<', $recipe->id)->count(),
            "filter[name:like]={$encodedNameSubstr}%" => Recipe::visible()->where('name', 'like', $nameSubstr . '%')->count(),

            // Test mulitple filters 'and' together
            "filter[id]={$recipe->id}&filter[name]=random_non_existing_string" => 0,
        ];

        foreach ($filterChecks as $filterOption => $resultCount) {
            $resp = $this->get($this->endpoint . '?count=1&' . $filterOption);
            $resp->assertJson(['total' => $resultCount]);
        }
    }

    public function test_total_on_results_shows_correctly()
    {
        $this->actingAsApiEditor();
        $recipeCount = Recipe::query()->count();
        $resp = $this->get($this->endpoint . '?count=1');
        $resp->assertJson(['total' => $recipeCount]);
    }

    public function test_total_on_results_shows_correctly_when_offset_provided()
    {
        $this->actingAsApiEditor();
        $recipeCount = Recipe::query()->count();
        $resp = $this->get($this->endpoint . '?count=1&offset=1');
        $resp->assertJson(['total' => $recipeCount]);
    }
}
