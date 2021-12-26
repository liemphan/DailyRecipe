<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddSearchIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // This was removed for v0.24 since these indexes are removed anyway
        // and will cause issues for db engines that don't support such indexes.

//        $prefix = DB::getTablePrefix();
//        DB::statement("ALTER TABLE {$prefix}pages ADD FULLTEXT search(name, text)");
//        DB::statement("ALTER TABLE {$prefix}recipes ADD FULLTEXT search(name, description)");
//        DB::statement("ALTER TABLE {$prefix}chapters ADD FULLTEXT search(name, description)");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $sm = Schema::getConnection()->getDoctrineSchemaManager();
        $pages = $sm->listTableDetails('pages');
        $recipes = $sm->listTableDetails('recipes');
        $chapters = $sm->listTableDetails('chapters');

        if ($pages->hasIndex('search')) {
            Schema::table('pages', function (Blueprint $table) {
                $table->dropIndex('search');
            });
        }

        if ($recipes->hasIndex('search')) {
            Schema::table('recipes', function (Blueprint $table) {
                $table->dropIndex('search');
            });
        }

        if ($chapters->hasIndex('search')) {
            Schema::table('chapters', function (Blueprint $table) {
                $table->dropIndex('search');
            });
        }
    }
}
