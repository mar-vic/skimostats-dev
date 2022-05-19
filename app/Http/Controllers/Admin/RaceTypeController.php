<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\RaceType as Entry;
use App\Race;

class RaceTypeController extends Controller
{
    private $slug = 'race-types';
    private $entityName = 'Race type';

    public function list(Request $request, Race $race) {

    }

    public function add(Request $request, Race $race) {
        return $this->edit($request, new Entry(), $race, false);
    }

    public function edit(Request $request, Entry $entry, Race $race, $edit = true) {

    }

    public function update(Request $request, Entry $entry, Race $race, $edit = true) {
        $request->validate([
            'name' => 'required',
        ]);

        return back()->with([
            'success' => 'Sucessfully saved ' . $entry->name . '.',
        ]);
    }

    public function create(Request $request, Race $race) {
        return $this->update($request, new Entry(), $race, false);
    }

    public function delete(Request $request, Entry $entry) {
        $name = $entry->name;
        $entry->delete();

        return back()->with([
            'success' => 'Successfully deleted ' . $name . '.'
        ]);
    }
}
