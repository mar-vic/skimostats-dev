<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRaceEventParticipantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('race_event_participants', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->bigInteger('raceEventId')->unsigned();
            $table->bigInteger('categoryId')->unsigned()->nullable();
            $table->bigInteger('athleteId')->unsigned()->nullable();
            $table->boolean('attended')->default(0);
            $table->boolean('disqualified')->default(0);
            $table->text('disqualifiedText')->nullable();
            $table->string('name')->nullable();
            $table->string('gender')->nullable();
            $table->bigInteger('countryId')->unsigned()->nullable();
            $table->bigInteger('raceEventTeamId')->unsigned()->nullable();

            $table->foreign('raceEventId')->references('id')->on('race_events')->onDelete('cascade');
            $table->foreign('categoryId')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('athleteId')->references('id')->on('athletes')->onDelete('set null');
            $table->foreign('countryId')->references('id')->on('countries')->onDelete('set null');
            $table->foreign('raceEventTeamId')->references('id')->on('race_event_teams')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('race_event_participants');
    }
}
