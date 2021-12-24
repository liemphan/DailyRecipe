<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateRecipemenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        // Convert the existing entity tables to InnoDB.
        // Wrapped in try-catch just in the event a different database system is used
        // which does not support InnoDB but does support all required features
        // like foreign key references.
        try {
            $prefix = DB::getTablePrefix();
            DB::statement("ALTER TABLE {$prefix}pages ENGINE = InnoDB;");
            DB::statement("ALTER TABLE {$prefix}chapters ENGINE = InnoDB;");
            DB::statement("ALTER TABLE {$prefix}books ENGINE = InnoDB;");
        } catch (Exception $exception) {
        }

        // Here we have table drops before the creations due to upgrade issues
        // people were having due to the recipemenus_books table creation failing.
        if (Schema::hasTable('recipemenus_books')) {
            Schema::drop('recipemenus_books');
        }

        if (Schema::hasTable('recipemenus')) {
            Schema::drop('recipemenus');
        }

        Schema::create('recipemenus', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 180);
            $table->string('slug', 180);
            $table->text('description');
            $table->integer('created_by')->nullable()->default(null);
            $table->integer('updated_by')->nullable()->default(null);
            $table->boolean('restricted')->default(false);
            $table->integer('image_id')->nullable()->default(null);
            $table->timestamps();

            $table->index('slug');
            $table->index('created_by');
            $table->index('updated_by');
            $table->index('restricted');
        });

        Schema::create('recipemenus_books', function (Blueprint $table) {
            $table->integer('recipemenu_id')->unsigned();
            $table->integer('book_id')->unsigned();
            $table->integer('order')->unsigned();

            $table->primary(['recipemenu_id', 'book_id']);

            $table->foreign('recipemenu_id')->references('id')->on('recipemenus')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('book_id')->references('id')->on('books')
                ->onUpdate('cascade')->onDelete('cascade');
        });

        // Delete old recipemenu permissions
        // Needed to to issues upon upgrade.
        DB::table('role_permissions')->where('name', 'like', 'recipemenu-%')->delete();

        // Copy existing role permissions from Books
        $ops = ['View All', 'View Own', 'Create All', 'Create Own', 'Update All', 'Update Own', 'Delete All', 'Delete Own'];
        foreach ($ops as $op) {
            $dbOpName = strtolower(str_replace(' ', '-', $op));
            $roleIdsWithBookPermission = DB::table('role_permissions')
                ->leftJoin('permission_role', 'role_permissions.id', '=', 'permission_role.permission_id')
                ->leftJoin('roles', 'roles.id', '=', 'permission_role.role_id')
                ->where('role_permissions.name', '=', 'book-' . $dbOpName)->get(['roles.id'])->pluck('id');

            $permId = DB::table('role_permissions')->insertGetId([
                'name'         => 'recipemenu-' . $dbOpName,
                'display_name' => $op . ' ' . 'RecipeMenus',
                'created_at'   => \Carbon\Carbon::now()->toDateTimeString(),
                'updated_at'   => \Carbon\Carbon::now()->toDateTimeString(),
            ]);

            $rowsToInsert = $roleIdsWithBookPermission->filter(function ($roleId) {
                return !is_null($roleId);
            })->map(function ($roleId) use ($permId) {
                return [
                    'role_id'       => $roleId,
                    'permission_id' => $permId,
                ];
            })->toArray();

            // Assign view permission to all current roles
            DB::table('permission_role')->insert($rowsToInsert);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop created permissions
        $ops = ['recipemenu-create-all', 'recipemenu-create-own', 'recipemenu-delete-all', 'recipemenu-delete-own', 'recipemenu-update-all', 'recipemenu-update-own', 'recipemenu-view-all', 'recipemenu-view-own'];

        $permissionIds = DB::table('role_permissions')->whereIn('name', $ops)
            ->get(['id'])->pluck('id')->toArray();
        DB::table('permission_role')->whereIn('permission_id', $permissionIds)->delete();
        DB::table('role_permissions')->whereIn('id', $permissionIds)->delete();

        // Drop menus table
        Schema::dropIfExists('recipemenus_books');
        Schema::dropIfExists('recipemenus');

        // Drop related polymorphic items
        DB::table('activities')->where('entity_type', '=', 'DailyRecipe\Entities\Models\Recipemenu')->delete();
        DB::table('views')->where('viewable_type', '=', 'DailyRecipe\Entities\Models\Recipemenu')->delete();
        DB::table('entity_permissions')->where('restrictable_type', '=', 'DailyRecipe\Entities\Models\Recipemenu')->delete();
        DB::table('tags')->where('entity_type', '=', 'DailyRecipe\Entities\Models\Recipemenu')->delete();
        DB::table('search_terms')->where('entity_type', '=', 'DailyRecipe\Entities\Models\Recipemenu')->delete();
        DB::table('comments')->where('entity_type', '=', 'DailyRecipe\Entities\Models\Recipemenu')->delete();
    }
}