<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Country;
use App\Team as Entry;
use App\Athlete;
use App\TeamAthlete;

class TeamController extends Controller
{
    private $slug = 'teams';
    private $entityName = 'Teams';

    public function list(Request $request) {
        $entries = Entry::orderBy('name', 'asc');

        if ($request->has('filter')) {
            $filter = $request->get('filter');
            $entries->where('name', 'LIKE', '%'.$filter.'%');
        }

        return view('admin.'.$this->slug.'.index', [
            'entries' => $entries->get(),
            'slug' => $this->slug,
            'entityName' => $this->entityName,
        ]);
    }

    public function addAthlete(Request $request, Entry $team) {
        $request->validate([
            'athleteId' => 'required|exists:athletes,id',
        ]);

        $athleteId = $request->get('athleteId');
        $athlete = Athlete::find($athleteId);
        $customName = $athlete->name;

        if ($request->get('customName')) {
            $customName = trim($request->get('customName'));
        }

        $positionName = $request->get('positionName');
        $order = $request->get('order');

        $options = [
            'athleteId' => $athleteId,
            'teamId' => $team->id,
        ];

        if (TeamAthlete::where($options)->first()) {
            return back()->withErrors([
                'name' => 'Athlete is already in the team.'
            ]);
        }

        $options['name'] = $customName;
        $options['positionName'] = $positionName;
        $options['countryId'] = $athlete->countryId;
        $options['order'] = $order;
        $teamAthlete = TeamAthlete::create($options);

        return back()->with('success', 'Athlete ' . $customName . ' was added.');
    }

    public function removeAthlete(Request $request, TeamAthlete $teamAthlete) {
        $athleteName = $teamAthlete->name;
        $teamAthlete->delete();

        return back()->with('success', 'Athlete ' . $athleteName . ' was removed from team.');
    }

    public function add(Request $request) {
        return $this->edit($request, new Entry(), false);
    }

    public function edit(Request $request, Entry $entry, $edit = true) {
        return view('admin.'.$this->slug.'.edit', [
            'edit' => $edit,
            'entry' => $entry,
            'slug' => $this->slug,
            'entityName' => $this->entityName,
            'countries' => Country::orderBy('name', 'asc')->get(),
        ]);
    }

    public function update(Request $request, Entry $entry) {
        $request->validate([
            'name' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg|max:4096',
        ]);

        $entry->fill($request->only([
            'name',
            'description',
        ]));

        if ($request->countryId != 0) {
            $entry->countryId = $request->countryId;
        }

        $entry->save();

        if ($request->hasFile('image')) {
            $entry->setImage($request->file('image'));
        }

        return back()->with([
            'success' => 'Sucessfully saved ' . $entry->name . '.',
        ]);
    }

    public function create(Request $request) {
        return $this->update($request, new Entry());
    }

    public function delete(Request $request, Entry $entry) {
        $name = $entry->name;
        $entry->delete();

        return back()->with([
            'success' => 'Successfully deleted ' . $name . '.'
        ]);
    }
}
