<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAthleteTopResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('athlete_top_results', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->bigInteger('raceEventId')->unsigned()->nullable();
            $table->bigInteger('athleteId')->unsigned()->nullable();
            $table->string('name');
            $table->integer('place')->default(1);
            $table->timestamp('race_date')->nullable();
            $table->integer('position')->default(1);

            $table->foreign('athleteId')->references('id')->on('athletes')->onDelete('cascade');
            $table->foreign('raceEventId')->references('id')->on('race_events')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('athlete_top_results');
    }
}
