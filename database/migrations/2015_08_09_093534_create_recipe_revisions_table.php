<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRecipeRevisionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recipe_revisions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('recipe_id')->indexed();
            $table->string('name');
            $table->longText('html');
            $table->longText('text');
            $table->integer('created_by');
            $table->nullableTimestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('recipe_revisions');
    }
}
