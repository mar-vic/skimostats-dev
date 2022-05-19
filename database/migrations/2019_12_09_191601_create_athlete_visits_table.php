<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAthleteVisitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('athlete_visits', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();

            $table->string('ipAddress');
            $table->text('userAgent');
            $table->bigInteger('athleteId')->unsigned();
            $table->datetime('lastVisit')->nullable();

            $table->foreign('athleteId')->references('id')->on('athletes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('athlete_visits');
    }
}
