<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAthletesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('athletes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('firstName');
            $table->string('lastName');
            $table->string('gender')->nullable();
            $table->bigInteger('countryId')->unsigned()->nullable()->index();
            $table->string('placeOfBirth')->nullable();
            $table->date('dateOfBirth')->nullable();
            $table->string('favoriteRace')->nullable();
            $table->bigInteger('favoriteRaceId')->unsigned()->nullable();
            $table->text('socialLinks')->nullable();
            $table->string('image')->nullable();

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
        Schema::dropIfExists('athletes');
    }
}
