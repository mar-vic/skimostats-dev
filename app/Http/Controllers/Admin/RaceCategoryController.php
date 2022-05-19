<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\RaceCategory as Entry;
use App\RaceType;
use App\Race;

class RaceCategoryController extends Controller
{
    private $slug = 'race-category';
    private $entityName = 'Race category';

    public function list(Request $request, RaceType $raceType) {
        $entries = Entry::orderBy('name', 'asc')->where('raceTypeId', $raceType->getKey());

        if ($request->has('filter')) {
            $filter = $request->get('filter');

            $entries->where('name', 'LIKE', '%'.$filter.'%');
        }

        return view('admin.'.$this->slug.'.index', [
            'entries' => $entries->get(),
            'race' => $raceType->race,
            'raceType' => $raceType,
            'slug' => $this->slug,
            'entityName' => $this->entityName,
        ]);
    }

    public function add(Request $request, RaceType $raceType) {
        return $this->edit($request, $raceType, new Entry(), false);
    }

    public function edit(Request $request, RaceType $raceType, Entry $entry, $edit = true) {
        if ($edit) {
            $raceType = $entry->raceType;
        }

        return view('admin.'.$this->slug.'.edit', [
            'edit' => $edit,
            'race' => $raceType->race,
            'raceType' => $raceType,
            'entry' => $entry,
            'slug' => $this->slug,
            'entityName' => $this->entityName,
        ]);
    }

    public function update(Request $request, RaceType $raceType, Entry $entry, $edit = true) {
        $request->validate([
            'name' => 'required',
        ]);

        $entry->fill($request->only([
            'name',
        ]));

        $entry->isMain = $request->filled('isMain');
        $entry->isTeam = $request->filled('isTeam');

        $race = $raceType->race;

        $entry->raceTypeId = $raceType->id;

        if ($race && $race->name) {
            $entry->raceId = $race->getKey();
        }

        $entry->save();

        return back()->with([
            'success' => 'Sucessfully saved ' . $entry->name . '.',
        ]);
    }

    public function create(Request $request, RaceType $raceType) {
        return $this->update($request, $raceType, new Entry(), false);
    }

    public function delete(Request $request, Entry $entry) {
        $name = $entry->name;
        $entry->delete();

        return back()->with([
            'success' => 'Successfully deleted ' . $name . '.'
        ]);
    }
}
