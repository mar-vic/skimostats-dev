<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRaceIdAndRaceEventIdToRankingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rankings', function (Blueprint $table) {
            $table->bigInteger('raceId')->unsigned()->nullable();
            $table->bigInteger('raceEventId')->unsigned()->nullable();
            $table->integer('rank')->nullable();
            $table->bigInteger('rankingCategoryId')->unsigned()->nullable();

            $table
                ->foreign('raceId')
                ->references('id')
                ->on('races')
                ->onDelete('set null');
            $table
                ->foreign('raceEventId')
                ->references('id')
                ->on('race_events')
                ->onDelete('set null');
            $table
                ->foreign('rankingCategoryId')
                ->references('id')
                ->on('ranking_categories')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rankings', function (Blueprint $table) {
            $table->dropForeign(['raceId']);
            $table->dropForeign(['raceEventId']);
            $table->dropForeign(['rankingCategoryId']);

            $table->dropColumn('raceId');
            $table->dropColumn('raceEventId');
            $table->dropColumn('rank');
            $table->dropColumn('rankingCategoryId');
        });
    }
}
