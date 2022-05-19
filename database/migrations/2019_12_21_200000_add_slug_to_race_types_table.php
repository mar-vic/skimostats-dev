<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\RaceType;

class AddSlugToRaceTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('race_types', function (Blueprint $table) {
            $table->string('slug')->nullable();
        });

        foreach (RaceType::all() as $type) {
            $type->save();
        }

        Schema::table('race_types', function (Blueprint $table) {
            $table->unique('slug');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('race_types', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
}
