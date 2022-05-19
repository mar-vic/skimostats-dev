<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Category;

class AddU18U20Categories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $cat = new Category();
        $cat->name = "U18";
        $cat->age = "junior";
        $cat->isU23 = false;
        $cat->slug = "u18";
        $cat->save();

        $cat = new Category();
        $cat->name = "U20";
        $cat->age = "junior";
        $cat->isU23 = false;
        $cat->slug = "u20";
        $cat->save();

        $cat = new Category();
        $cat->name = "Mixed";
        $cat->age = "adult";
        $cat->isU23 = false;
        $cat->slug = "mixed";
        $cat->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
