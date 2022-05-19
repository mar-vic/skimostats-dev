<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\PartnerCategory as Entry;

class PartnerCategoryController extends Controller
{
    private $slug = 'partner-categories';
    private $entityName = 'Partner categories';

    public function list(Request $request) {
        $entries = Entry::orderBy('position', 'asc')->orderBy('name', 'asc');

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
        ]);
    }

    public function update(Request $request, Entry $entry, $edit = true) {
        $request->validate([
            'name' => 'required',
            'position' => 'required',
        ]);

        $entry->fill($request->only([
            'name',
            'position',
        ]));

        $entry->save();

        return redirect(route('admin.partner-categories.edit', $entry->id))->with([
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

    public function createEntry(Request $request, Entry $entry) {
        $request->validate([
            'entryName' => 'required',
            'url' => 'required',
            'image' => 'required|image'
        ]);
        $entries = json_decode($entry->partners, true) ?? [];
        $file = $request->file('image')->store('partners', ['disk' => 'pub']);

        $entries[] = [
            'name' => $request->get('entryName'),
            'url' => $request->get('url'),
            'image' => $file,
        ];

        $entry->partners = json_encode(array_values($entries));
        $entry->save();

        return back()->with(['success' => 'Partner was added.']);
    }

    public function deleteEntry(Request $request, Entry $entry, int $index) {
        $entries = $entry->entries;
        $partner = $entries[$index];
        if (file_exists("uploads/".$partner->image)) {
            unlink(public_path("uploads/".$partner->image));
        }
        unset($entries[$index]);

        $entry->partners = json_encode(array_values($entries));

        $entry->save();

        return back()->with(['success' => 'Entry was deleted.']);
    }

    public function saveEntryOrder(Request $request, Entry $entry) {
        $request->validate([
            'order' => 'required|array',
        ]);
        $order = $request->get('order');
        $newEntries = [];
        $entries = $entry->entries;

        foreach($order as $index) {
            $newEntries[] = $entries[$index];
        }

        $entry->partners = json_encode(array_values($newEntries));
        $entry->save();

        return back()->with(['success' => 'Entry order was saved.']);
    }
}
