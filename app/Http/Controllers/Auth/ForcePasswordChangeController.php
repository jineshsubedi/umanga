<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class ForcePasswordChangeController extends Controller
{
    /**
     * Display the force password change view.
     */
    public function show(Request $request)
    {
        // If they already changed it, redirect them to dashboard
        if (!is_null($request->user()->password_changed_at)) {
            return redirect()->route('dashboard');
        }

        return Inertia::render('Auth/ForcePasswordChange');
    }

    /**
     * Handle an incoming new password request.
     */
    public function store(Request $request)
    {
        if (!is_null($request->user()->password_changed_at)) {
            return redirect()->route('dashboard');
        }

        $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $request->user()->update([
            'password' => Hash::make($request->password),
            'password_changed_at' => now(),
        ]);

        return redirect()->route('dashboard')->with('success', 'Password successfully changed. Welcome!');
    }
}
