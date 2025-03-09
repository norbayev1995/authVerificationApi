<?php

namespace App\Http\Controllers;

use App\Mail\MailVerification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
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
        $mail = Mail::to($user->email)->send(new MailVerification($user));
        return response()->json([
            'message' => 'User created successfully',
            'user' => $user
        ], 201);
    }

    public function verify(Request $request)
    {
        $token = $request->query('token');
        $user = User::where('verification_token', $token)->first();
        $user->email_verified_at = now();
        $user->update();
        $auth_token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'token' => $auth_token,
        ], 200);
    }
}
