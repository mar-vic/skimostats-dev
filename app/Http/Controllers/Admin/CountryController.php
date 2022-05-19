<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Country;

class CountryController extends Controller
{
    public function list(Request $request) {
        return view('admin.countries.index', ['countries' => Country::orderBy('name', 'asc')->get()]);
    }
}
