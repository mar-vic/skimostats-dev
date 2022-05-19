<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Race as Entry;
use App\RaceCategory;
use App\RaceType;
use App\RankingCategory;

class RaceController extends Controller
{
    private $slug = 'races';
    private $entityName = 'Races';

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
        return view('admin.'.$this->slug.'.edit', [
            'edit' => $edit,
            'entry' => $entry,
            'slug' => $this->slug,
            'entityName' => $this->entityName,
            'rankingCategories' => RankingCategory::orderBy('position','asc')->orderBy('id', 'asc')->get(),
        ]);
    }

    public function update(Request $request, Entry $entry, $edit = true) {
        $request->validate([
            'name' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg|max:4096',
        ]);

        $entry->fill($request->only([
            'name',
            'bgText',
            'alternativeName',
            'description',
            'place',
            'metaDescription',
            'metaKeywords',
            'yearStart',
            'yearEnd',
        ]));

        if ($request->get('rankingCategoryId')) {
            $entry->rankingCategoryId = $request->get('rankingCategoryId');
        }

        $entry->save();

        if ($request->hasFile('image')) {
            $entry->setImage($request->file('image'));
        }

        return redirect(route('admin.races.edit', $entry->id))->with([
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
