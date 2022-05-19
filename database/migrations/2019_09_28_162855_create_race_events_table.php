<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRaceEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('race_events', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->bigInteger('raceId')->unsigned();
            $table->timestamp('startDate')->nullable();
            $table->timestamp('endDate')->nullable();
            $table->integer('year')->nullable();
            $table->integer('elevation')->nullable();
            $table->string('place')->nullable();
            $table->bigInteger('type')->unsigned()->nullable();
            $table->bigInteger('parent')->unsigned()->nullable();
            $table->boolean('hasStages')->default(false);
            $table->boolean('isTeam')->default(false);
            $table->boolean('is_visible')->default(false);

            $table->foreign('parent')->references('id')->on('race_events')->onDelete('cascade');
            $table->foreign('raceId')
                ->references('id')->on('races')
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
        Schema::dropIfExists('race_events');
    }
}
