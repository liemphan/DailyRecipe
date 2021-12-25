<?php

namespace Database\Seeders;

use DailyRecipe\Api\ApiToken;
use DailyRecipe\Auth\Permissions\PermissionService;
use DailyRecipe\Auth\Permissions\RolePermission;
use DailyRecipe\Auth\Role;
use DailyRecipe\Auth\User;
use DailyRecipe\Entities\Models\Recipemenu;
use DailyRecipe\Entities\Models\Chapter;
use DailyRecipe\Entities\Models\Page;
use DailyRecipe\Entities\Tools\SearchIndex;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DummyContentSeeder extends Seeder
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

        // Create a viewer user
        $viewerUser = User::factory()->create();
        $role = Role::getRole('viewer');
        $viewerUser->attachRole($role);

        $byData = ['created_by' => $editorUser->id, 'updated_by' => $editorUser->id, 'owned_by' => $editorUser->id];

        \DailyRecipe\Entities\Models\Recipe::factory()->count(5)->create($byData)
            ->each(function ($book) use ($byData) {
                $chapters = Chapter::factory()->count(3)->create($byData)
                    ->each(function ($chapter) use ($book, $byData) {
                        $pages = Page::factory()->count(3)->make(array_merge($byData, ['recipe_id' => $book->id]));
                        $chapter->pages()->saveMany($pages);
                    });
                $pages = Page::factory()->count(3)->make($byData);
                $book->chapters()->saveMany($chapters);
                $book->pages()->saveMany($pages);
            });

        $largeBook = \DailyRecipe\Entities\Models\Recipe::factory()->create(array_merge($byData, ['name' => 'Large book' . Str::random(10)]));
        $pages = Page::factory()->count(200)->make($byData);
        $chapters = Chapter::factory()->count(50)->make($byData);
        $largeBook->pages()->saveMany($pages);
        $largeBook->chapters()->saveMany($chapters);

        $menus = Recipemenu::factory()->count(10)->create($byData);
        $largeBook->menus()->attach($menus->pluck('id'));

        // Assign API permission to editor role and create an API key
        $apiPermission = RolePermission::getByName('access-api');
        $editorRole->attachPermission($apiPermission);
        $token = (new ApiToken())->forceFill([
            'user_id'    => $editorUser->id,
            'name'       => 'Testing API key',
            'expires_at' => ApiToken::defaultExpiry(),
            'secret'     => Hash::make('password'),
            'token_id'   => 'apitoken',
        ]);
        $token->save();

        app(PermissionService::class)->buildJointPermissions();
        app(SearchIndex::class)->indexAllEntities();
    }
}
