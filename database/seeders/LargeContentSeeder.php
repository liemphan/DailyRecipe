<?php

namespace Database\Seeders;

use DailyRecipe\Auth\Permissions\PermissionService;
use DailyRecipe\Auth\Role;
use DailyRecipe\Auth\User;
use DailyRecipe\Entities\Models\Recipe;
use DailyRecipe\Entities\Models\Chapter;
use DailyRecipe\Entities\Models\Page;
use DailyRecipe\Entities\Tools\SearchIndex;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LargeContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create an editor user
        $editorUser = User::factory()->create();
        $editorRole = Role::getRole('editor');
        $editorUser->attachRole($editorRole);

        /** @var Recipe $largeRecipe */
        $largeRecipe = Recipe::factory()->create(['name' => 'Large recipe' . Str::random(10), 'created_by' => $editorUser->id, 'updated_by' => $editorUser->id]);
        $pages = Page::factory()->count(200)->make(['created_by' => $editorUser->id, 'updated_by' => $editorUser->id]);
        $chapters = Chapter::factory()->count(50)->make(['created_by' => $editorUser->id, 'updated_by' => $editorUser->id]);

        $largeRecipe->pages()->saveMany($pages);
        $largeRecipe->chapters()->saveMany($chapters);
        $all = array_merge([$largeRecipe], array_values($pages->all()), array_values($chapters->all()));

        app()->make(PermissionService::class)->buildJointPermissionsForEntity($largeRecipe);
        app()->make(SearchIndex::class)->indexEntities($all);
    }
}
