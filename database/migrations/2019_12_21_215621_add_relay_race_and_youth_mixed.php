<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\RaceType;
use App\Category;

class AddRelayRaceAndYouthMixed extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $type = new RaceType();
        $type->name = "Relay race";
        $type->isTeam = 1;
        $type->type = 5;
        $type->save();

        $cat = new Category();
        $cat->name = "Youth Mixed";
        $cat->age = "junior";
        $cat->isU23 = false;
        $cat->slug = "youth-mixed";
        $cat->save();
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
