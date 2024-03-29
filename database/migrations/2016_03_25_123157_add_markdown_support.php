<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddMarkdownSupport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('recipes', function (Blueprint $table) {
            $table->longText('markdown')->default('');
        });

        Schema::table('recipe_revisions', function (Blueprint $table) {
            $table->longText('markdown')->default('');
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
            $table->dropColumn('markdown');
        });

        Schema::table('recipe_revisions', function (Blueprint $table) {
            $table->dropColumn('markdown');
        });
    }
}
