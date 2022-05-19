<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRaceEventStagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('race_event_stages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->bigInteger('raceEventId')->unsigned();
            $table->bigInteger('categoryId')->unsigned()->nullable();
            $table->integer('stage')->default(1);
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->string('image')->nullable();

            $table->foreign('raceEventId')->references('id')->on('race_events')->onDelete('cascade');
            $table->foreign('categoryId')->references('id')->on('categories')->onDelete('cascade');
        });

        Schema::table('race_event_entries', function (Blueprint $table) {
            $table->bigInteger('raceEventStageId')->after('raceEventId')->unsigned()->nullable();

            $table->foreign('raceEventStageId')
                ->references('id')->on('race_event_stages')
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
        Schema::table('race_event_entries', function (Blueprint $table) {
            $table->dropColumn('raceEventStageId');
        });

        Schema::dropIfExists('race_event_stages');
    }
}
