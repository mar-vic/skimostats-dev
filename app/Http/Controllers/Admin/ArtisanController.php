<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use \Artisan;

class ArtisanController extends Controller
{
    public function migrate() {
        return Artisan::call('migrate');
    }

    public function dumpAutoload() {
        Artisan::call('dump-autoload');
        echo 'dump-autoload complete';
    }
}
