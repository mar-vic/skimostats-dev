<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRaceEventEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('race_event_entries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->bigInteger('raceEventId')->unsigned();
            $table->bigInteger('categoryId')->unsigned()->nullable();
            $table->bigInteger('raceEventParticipantId')->unsigned()->nullable();
            $table->bigInteger('raceEventTeamId')->unsigned()->nullable();
            $table->string('timeRaw')->nullable();
            $table->integer('time')->nullable();
            $table->integer('rank')->nullable();
            $table->string('status')->nullable();

            $table->foreign('raceEventId')
                ->references('id')->on('race_events')
                ->onDelete('cascade');
            $table->foreign('categoryId')
                ->references('id')->on('categories')
                ->onDelete('cascade');
            $table->foreign('raceEventParticipantId')
                ->references('id')->on('race_event_participants')
                ->onDelete('cascade');
            $table->foreign('raceEventTeamId')
                ->references('id')->on('race_event_teams')
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
        Schema::dropIfExists('race_event_entries');
    }
}
