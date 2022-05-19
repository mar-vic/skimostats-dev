<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRaceEventTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('race_event_teams', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('name');
            $table->bigInteger('teamId')->unsigned()->nullable();
            $table->bigInteger('raceEventId')->unsigned();
            $table->bigInteger('categoryId')->unsigned();
            $table->bigInteger('countryId')->unsigned()->nullable();

            $table->foreign('teamId')
                ->references('id')->on('teams');
            $table->foreign('raceEventId')
                ->references('id')->on('race_events')
                ->onDelete('cascade');
            $table->foreign('categoryId')
                ->references('id')->on('categories')
                ->onDelete('cascade');
            $table->foreign('countryId')
                ->references('id')->on('countries')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('race_event_teams');
    }
}
