<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRankingTablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ranking_tables', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();

            $table->integer('type')->unsigned();
            $table->bigInteger('athleteId')->unsigned();
            $table->integer('rank')->unsigned();
            $table->integer('rankBefore')->unsigned()->nullable();
            $table->integer('lastRankChange')->unsigned()->nullable();
            $table->integer('points')->nullable();
            $table->integer('pointsBefore')->nullable();
            $table->datetime('dateBefore')->nullable();
            $table->integer('year')->nullable();
            $table->bigInteger('categoryId')->unsigned();

            $table->foreign('athleteId')->references('id')->on('athletes')->onDelete('cascade');
            $table->foreign('categoryId')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ranking_tables', function (Blueprint $table) {
            $table->dropForeign(['athleteId']);
            $table->dropForeign(['categoryId']);
        });

        Schema::dropIfExists('ranking_tables');
    }
}
