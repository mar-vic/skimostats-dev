<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\RankingCategory as Entry;

class RankingCategoryController extends Controller
{
    private $slug = 'ranking-categories';
    private $entityName = 'Ranking categories';

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

    public function add(Request $request) {
        return $this->edit($request, new Entry(), false);
    }

    public function edit(Request $request, Entry $entry, $edit = true) {
        $rankPointMap = $entry->rankPointMap ? json_decode($entry->rankPointMap, true) : null;
        $ranks = $rankPointMap ? array_keys($rankPointMap) : old('mapRank', []);
        $points = $rankPointMap ? array_values($rankPointMap) : old('mapPoint', []);

        return view('admin.'.$this->slug.'.edit', [
            'edit' => $edit,
            'entry' => $entry,
            'ranks' => $ranks,
            'points' => $points,
            'slug' => $this->slug,
            'entityName' => $this->entityName,
        ]);
    }

    public function update(Request $request, Entry $entry, $edit = true) {
        $request->validate([
            'name' => 'required',
            'mapRank' => 'required|array',
            'mapPoint' => 'required|array',
        ]);

        $ranks = array_map('intval', $request->get('mapRank'));
        $points = array_map('intval', $request->get('mapPoint'));

        $rankPointMap = array_filter(
            array_combine($ranks, $points),
            function ($val, $key) {
                return $key;
            },
            ARRAY_FILTER_USE_BOTH
        );

        $entry->name = $request->get('name');
        $entry->rankPointMap = json_encode($rankPointMap);

        $entry->save();

        return redirect(route('admin.'.$this->slug.'.edit', $entry->id))->with([
            'success' => 'Sucessfully saved ' . $entry->name . '.',
        ]);
    }

    public function create(Request $request) {
        return $this->update($request, new Entry(), false);
    }

    public function delete(Request $request, Entry $entry) {
        $name = $entry->name;

        $entry->delete();

        return back()->with([
            'success' => 'Successfully deleted ' . $name . '.'
        ]);
    }
}
