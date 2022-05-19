<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use Auth;

use Illuminate\Http\Request;
use App\RaceEvent;
use App\RaceCategory;
use App\RaceEventEntry;

class HomeController extends Controller
{

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function welcome() {
        if (Auth::check()) {
            return redirect('/');
        }
        return view('welcome', [
        ]);
    }

}
