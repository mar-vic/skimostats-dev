<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNecessaryStageColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('race_events', function(Blueprint $table) {
            $table->integer('stageNumber')->nullable();
            $table->boolean('isGeneralClassification')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('race_events', function(Blueprint $table) {
            $table->dropColumn('stageNumber');
            $table->dropColumn('isGeneralClassification');
        });
    }
}
