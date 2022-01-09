<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSearchIndexTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('search_terms', function (Blueprint $table) {
            $table->increments('id');
            $table->string('term', 180);
            $table->string('entity_type', 100);
            $table->integer('entity_id');
            $table->integer('score');

            $table->index('term');
            $table->index('entity_type');
            $table->index(['entity_type', 'entity_id']);
            $table->index('score');
        });

        $sm = Schema::getConnection()->getDoctrineSchemaManager();

        $recipes = $sm->listTableDetails('recipes');


        if ($recipes->hasIndex('search')) {
            Schema::table('recipes', function (Blueprint $table) {
                $table->dropIndex('search');
                $table->dropIndex('name_search');
            });
        }


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // This was removed for v0.24 since these indexes are removed anyway
        // and will cause issues for db engines that don't support such indexes.

//        $prefix = DB::getTablePrefix();
//        DB::statement("ALTER TABLE {$prefix}pages ADD FULLTEXT search(name, text)");
//        DB::statement("ALTER TABLE {$prefix}recipes ADD FULLTEXT search(name, description)");
//        DB::statement("ALTER TABLE {$prefix}chapters ADD FULLTEXT search(name, description)");
//        DB::statement("ALTER TABLE {$prefix}pages ADD FULLTEXT name_search(name)");
//        DB::statement("ALTER TABLE {$prefix}recipes ADD FULLTEXT name_search(name)");
//        DB::statement("ALTER TABLE {$prefix}chapters ADD FULLTEXT name_search(name)");

        Schema::dropIfExists('search_terms');
    }
}
