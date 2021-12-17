<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEntitySoftDeletes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('recipes', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('chapters', function (Blueprint $table) {
            $table->softDeletes();
        });
        Schema::table('pages', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('recipes', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('chapters', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        Schema::table('pages', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
}
