<?php

use Illuminate\Database\Migrations\Migration;

class AddSummaryToPageRevisions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('recipe_revisions', function ($table) {
            $table->string('summary')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('recipe_revisions', function ($table) {
            $table->dropColumn('summary');
        });
    }
}
