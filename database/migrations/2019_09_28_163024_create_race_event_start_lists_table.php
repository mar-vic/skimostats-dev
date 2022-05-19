<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRaceEventStartListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('race_event_start_list', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->bigInteger('athleteId')->unsigned();
            $table->bigInteger('categoryId')->unsigned()->nullable();
            $table->integer('position')->default(0);

            $table->foreign('categoryId')
                ->references('id')->on('categories')
                ->onDelete('cascade');
            $table->foreign('athleteId')
                ->references('id')->on('athletes')
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
        Schema::dropIfExists('race_event_start_list');
    }
}
