<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRankingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rankings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->integer('type')->default(1);
            $table->integer('points')->default(0);
            $table->bigInteger('athleteId')->unsigned();
            $table->bigInteger('participantId')->unsigned()->nullable();
            $table->bigInteger('categoryId')->unsigned()->nullable();
            $table->bigInteger('raceTypeId')->unsigned()->nullable();
            $table->bigInteger('addedBy')->unsigned()->nullable();
            $table->string('reason')->nullable();
            $table->datetime('obtainedAt')->nullable();

            $table->foreign('athleteId')->references('id')->on('athletes')->onDelete('cascade');
            $table->foreign('participantId')->references('id')->on('race_event_participants')->onDelete('set null');
            $table->foreign('categoryId')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('raceTypeId')->references('id')->on('race_types')->onDelete('set null');
            $table->foreign('addedBy')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rankings');
    }
}
