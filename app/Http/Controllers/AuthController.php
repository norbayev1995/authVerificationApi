<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = $request->password;
        $user->verification_token = Str::random(64);
        if ($request->hasFile('avatar')) {
            $user->avatar = $this->uploadPhoto($request->file('avatar'), 'avatars');
        }
        $user->save();
        return response()->json([
            'message' => 'User created successfully',
            'user' => $user
        ], 201);
    }
}
