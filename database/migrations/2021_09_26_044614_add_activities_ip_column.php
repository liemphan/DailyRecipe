<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddActivitiesIpColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->string('ip', 45)->after('user_id');
        });
        Schema::table('books', function (Blueprint $table) {
             Schema::rename('books','recipes');
         });

         Schema::table('pages', function (Blueprint $table) {
             $table->renameColumn('book_id', 'recipe_id');
         });
         Schema::table('chapters', function (Blueprint $table) {
             $table->renameColumn('book_id', 'recipe_id');
         });
         Schema::table('bookshelves', function (Blueprint $table) {
             Schema::rename('bookshelves','recipemenus');
         });
                 Schema::table('bookshelves_books', function (Blueprint $table) {
             Schema::rename('bookshelves_books','recipemenus_recipes');
         });
         Schema::table('recipemenus_recipes', function (Blueprint $table) {
             $table->renameColumn('book_id', 'recipe_id');
         });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn('ip');
        });
            Schema::drop('recipes');
            Schema::drop('recipemenus');
            Schema::drop('recipemenus_recipes');
            Schema::drop('pages');
            Schema::drop('chapters');
    }
}
