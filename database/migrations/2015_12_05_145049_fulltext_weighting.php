<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class FulltextWeighting extends Migration
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
//        DB::statement("ALTER TABLE {$prefix}pages ADD FULLTEXT name_search(name)");
//        DB::statement("ALTER TABLE {$prefix}recipes ADD FULLTEXT name_search(name)");
//        DB::statement("ALTER TABLE {$prefix}chapters ADD FULLTEXT name_search(name)");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $sm = Schema::getConnection()->getDoctrineSchemaManager();
        $recipes = $sm->listTableDetails('recipes');


        if ($recipes->hasIndex('name_search')) {
            Schema::table('recipes', function (Blueprint $table) {
                $table->dropIndex('name_search');
            });
        }

    }
}
