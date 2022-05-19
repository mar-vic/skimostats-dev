<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeamAthletesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('team_athletes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();

            $table->string('name');
            $table->bigInteger('athleteId')->unsigned();
            $table->bigInteger('teamId')->unsigned();
            $table->bigInteger('countryId')->unsigned()->nullable();
            $table->string('positionName')->nullable();
            $table->integer('position')->default(0);

            $table->foreign('countryId')
                ->references('id')->on('countries')->onDelete('set null');
            $table->foreign('athleteId')
                ->references('id')->on('athletes')
                ->onDelete('cascade');
            $table->foreign('teamId')
                ->references('id')->on('teams')
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
        Schema::dropIfExists('team_athletes');
    }
}
