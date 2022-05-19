<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use App\Category;

class CategoryController extends Controller
{
    public function index(Request $request) {
        $entries = Category::orderByRaw('ISNULL(priority) ASC')
            ->orderBy('priority', 'asc')
            ->orderBy('id', 'ASC')
            ->get();
        return view('admin.categories.index', [
            'entries' => $entries,
        ]);
    }

    public function create(Request $request) {
        $request->validate([
            'name' => 'required|min:2',
        ]);

        $cat = new Category();
        $cat->name = $request->get('name');
        $cat->gender = $request->get('gender');
        $cat->age = $request->get('age');
        $cat->save();

        return back()->with('success', 'Category was created.');
    }

    public function update(Request $request, Category $category) {
        $request->validate([
            'name' => 'required|min:2'
        ]);
        $category->name = $request->get('name');
        $category->priority = $request->get('priority');
        $category->age = $request->get('age');
        $category->gender = $request->get('gender');
        $category->save();

        return back()->with('success', 'Category was updated.');
    }

    public function edit(Request $request, Category $category) {
        return view('admin.categories.edit', [
            'category' => $category,
        ]);
    }
}
