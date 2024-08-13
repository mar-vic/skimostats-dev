<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class ApiUsageController extends Controller
{
    private $slug = 'users';
    public function index(Request $request)
    {
        return view('admin.users.index', [
            'slug' => $this->slug,
            'entries' => User::whereNotNull('api_token')->get()
        ]);
    }

    public function create(Request $request)
    {
        $fields = $request->validate([
            "name" => "required|string",
            "email" => "required|string|unique:users,email"
        ]);

        // dd($fields);

        $user = User::create([
            "name" => $fields["name"],
            "email" => $fields["email"]
        ]);

        // dd($user);

        $user->api_token = $user->createToken("skimotoken")->plainTextToken;
        $user->save();

        // $user = new User();
        // $user->name = $request->get('name');
        // $token = Str::random(80);
        //
        // $user->api_token = $token;
        // $user->save();

        return back();
    }

    public function delete($id)
    {
        User::destroy([$id]);
        return back();
    }
}
