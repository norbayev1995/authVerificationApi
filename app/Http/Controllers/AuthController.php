<?php

namespace App\Http\Controllers;

use App\Jobs\SendVerificationEmail;
use App\Mail\MailVerification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
        SendVerificationEmail::dispatch($user);
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

    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'The provided credentials are incorrect.',
            ], 401);
        }
        if ($user->email_verified_at === null) {
            return response()->json([
                'message' => 'Please verify your email before logging in.'
            ], 403);
        }
        $auth_token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'token' => $auth_token,
            'message' => 'Logged in successfully',
        ], 200);
    }
}
