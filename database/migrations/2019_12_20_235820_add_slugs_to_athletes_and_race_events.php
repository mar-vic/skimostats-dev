<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Athlete;
use App\RaceEvent;

class AddSlugsToAthletesAndRaceEvents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        config(['sluggable.onUpdate' => true]);
        // generate slug for every athlete
        Athlete::all()->each->save();
        // generate slug for every race event
        RaceEvent::all()->each->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
