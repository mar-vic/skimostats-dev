<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCountryColumnToRaceEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('race_events', function (Blueprint $table) {
            $table->bigInteger('countryId')->unsigned()->nullable();

            $table->foreign('countryId')
                ->references('id')
                ->on('countries')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('race_events', function (Blueprint $table) {
            $table->dropIndex('race_events_countryid_foreign');
            $table->dropColumn('countryId');
        });
    }
}
