<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Race;

class AddSlugToRacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('races', function (Blueprint $table) {
            $table->string('slug')->nullable()->unique();
            $table->string('alternativeName')->nullable();
            $table->string('bgText')->nullable();
            $table->datetime('yearStart')->nullable();
            $table->datetime('yearEnd')->nullable();
            $table->text('metaKeywords')->nullable();
            $table->text('metaDescription')->nullable();
        });

        Race::all()->each(function($item) {
            $item->save();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('races', function (Blueprint $table) {
            $table->dropColumn('slug');
            $table->dropColumn('alternativeName');
            $table->dropColumn('bgText');
            $table->dropColumn('yearStart');
            $table->dropColumn('yearEnd');
            $table->dropColumn('metaKeywords');
            $table->dropColumn('metaDescription');
        });
    }
}
