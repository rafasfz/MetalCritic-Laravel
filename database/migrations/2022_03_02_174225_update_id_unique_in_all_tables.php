<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateIdUniqueInAllTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('id')->nullable(false)->unique(true)->change();
        });

        Schema::table('games', function (Blueprint $table) {
            $table->uuid('id')->nullable(false)->unique(true)->change();
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->uuid('id')->nullable(false)->unique(true)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('reviews', function (Blueprint $table) {
            $table->uuid('id')->unique(false)->change();
        });

        Schema::table('games', function (Blueprint $table) {
            $table->uuid('id')->unique(false)->change();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->uuid('id')->unique(false)->change();
        });
    }
}
