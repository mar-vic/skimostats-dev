<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRaceEventCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('race_event_categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->bigInteger('raceEventId')->unsigned()->nullable();
            $table->bigInteger('categoryId')->unsigned()->nullable();

            $table->foreign('raceEventId')
                ->references('id')->on('race_events')
                ->onDelete('cascade');

            $table->foreign('categoryId')
                ->references('id')->on('categories')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('race_event_race_categories');
    }
}
