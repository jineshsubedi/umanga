<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials do not match our records.'],
            ]);
        }

        $user = Auth::user();

        // Optional: check user status here if needed
        if ($user->status !== 'active') {
            Auth::logout();
            return response()->json([
                'message' => 'Your account is inactive. Please contact your administrator.'
            ], 403);
        }

        $token = $user->createToken('mobile-app-token')->plainTextToken;

        return response()->json([
            'user' => new \App\Http\Resources\UserResource($user),
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }

    public function user(Request $request)
    {
        return response()->json(new \App\Http\Resources\UserResource($request->user()));
    }
}
