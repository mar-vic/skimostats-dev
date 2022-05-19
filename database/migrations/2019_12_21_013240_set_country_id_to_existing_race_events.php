<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\RaceEvent;
use App\Country;

class SetCountryIdToExistingRaceEvents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // $raceEvents = RaceEvent::whereNull('countryId')->whereNotNull('place')->get();
        // foreach($raceEvents as $event) {
        //     $exp = explode(",", $event->place);
        //     if (count($exp) > 1) {
        //         $countryName = trim($exp[count($exp) - 1]);

        //         $country = Country::where('name', $countryName)->first();
        //         if ($country) {
        //             $event->countryId = $country->id;
        //             $event->save();
        //         }
        //     }
        // }
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
