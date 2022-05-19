<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRankingCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ranking_categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();

            $table->string('name');
            $table->longText('rankPointMap');
            $table->integer('position')->default(0);
        });

        Schema::table('races', function (Blueprint $table) {
            $table
                ->bigInteger('rankingCategoryId')
                ->unsigned()
                ->nullable();

            $table
                ->foreign('rankingCategoryId')
                ->references('id')
                ->on('ranking_categories')
                ->onDelete('set null');
        });

        Schema::table('race_events', function (Blueprint $table) {
            $table
                ->bigInteger('rankingCategoryId')
                ->unsigned()
                ->nullable();

            $table
                ->foreign('rankingCategoryId')
                ->references('id')
                ->on('ranking_categories')
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
        Schema::dropIfExists('ranking_categories');

        Schema::table('races', function (Blueprint $table) {
            $table->dropForeign(['rankingCategoryId']);
            $table->dropColumn('rankingCategoryId');
        });

        Schema::table('race_events', function (Blueprint $table) {
            $table->dropForeign(['rankingCategoryId']);
            $table->dropColumn('rankingCategoryId');
        });
    }
}
