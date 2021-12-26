<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddSlugToRevisions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('page_revisions', function (Blueprint $table) {
            $table->string('slug');
            $table->index('slug');
            $table->string('recipe_slug');
            $table->index('recipe_slug');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('page_revisions', function (Blueprint $table) {
            $table->dropColumn('slug');
            $table->dropColumn('recipe_slug');
        });
    }
}
