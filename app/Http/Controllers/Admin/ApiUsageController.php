<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ApiUsageController extends Controller
{
    private $slug = 'users';
    public function index(Request $request) {
        return view('admin.users.index', [
            'slug' => $this->slug,
            'entries' => User::whereNotNull('api_token')->get()
        ]);
    }

    public function create(Request $request) {
        $request->validate([
            'name' => 'required'
        ]);

        $user = new User();
        $user->name = $request->get('name');
        $token = Str::random(80);

        $user->api_token = $token;
        $user->save();

        return back();
    }
}
