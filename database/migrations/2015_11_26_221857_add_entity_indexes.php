<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddEntityIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('recipes', function (Blueprint $table) {
            $table->index('slug');
            $table->index('priority');
            $table->index('created_by');
            $table->index('updated_by');
        });

        Schema::table('recipe_revisions', function (Blueprint $table) {
            $table->index('recipe_id');
        });

        Schema::table('activities', function (Blueprint $table) {
            $table->index('recipe_id');
            $table->index('user_id');
            $table->index('entity_id');
        });
        Schema::table('views', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('viewable_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('recipes', function (Blueprint $table) {
            $table->dropIndex('recipes_slug_index');
            $table->dropIndex('recipes_priority_index');
            $table->dropIndex('recipes_created_by_index');
            $table->dropIndex('recipes_updated_by_index');

        });

        Schema::table('recipe_revisions', function (Blueprint $table) {
            $table->dropIndex('page_revisions_page_id_index');
        });

        Schema::table('activities', function (Blueprint $table) {
            $table->dropIndex('activities_recipe_id_index');
            $table->dropIndex('activities_user_id_index');
            $table->dropIndex('activities_entity_id_index');
        });
        Schema::table('views', function (Blueprint $table) {
            $table->dropIndex('views_user_id_index');
            $table->dropIndex('views_viewable_id_index');
        });
    }
}
