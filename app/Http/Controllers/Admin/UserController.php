<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('company_id', auth()->user()->company_id)
            ->where('role', '!=', 'super_admin')
            ->withCount('meetingMemos')
            ->latest()
            ->get();

        return Inertia::render('Admin/Users/Index', compact('users'));
    }

    public function create()
    {
        return Inertia::render('Admin/Users/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users',
            'role'        => 'required|in:manager,staff',
            'designation' => 'nullable|string|max:255',
            'password'    => ['required', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'company_id'  => auth()->user()->company_id,
            'name'        => $request->name,
            'email'       => $request->email,
            'role'        => $request->role,
            'designation' => $request->designation,
            'password'    => Hash::make($request->password),
            'status'      => 'active',
        ]);

        event(new Registered($user));

        return redirect()->route('admin.users.index')->with('success', 'User created successfully. Verification email sent.');
    }

    public function edit(User $user)
    {
        abort_if($user->company_id !== auth()->user()->company_id, 403);
        return Inertia::render('Admin/Users/Edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        abort_if($user->company_id !== auth()->user()->company_id, 403);

        $request->validate([
            'name'        => 'required|string|max:255',
            'role'        => 'required|in:manager,staff',
            'designation' => 'nullable|string|max:255',
            'status'      => 'required|in:active,inactive',
        ]);

        $user->update($request->only('name', 'role', 'designation', 'status'));

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        abort_if($user->company_id !== auth()->user()->company_id, 403);
        $user->update(['status' => 'inactive']);
        return back()->with('success', 'User deactivated successfully.');
    }

    public function toggleStatus(User $user)
    {
        abort_if($user->company_id !== auth()->user()->company_id, 403);
        abort_if($user->role === 'super_admin' || $user->id === auth()->id(), 403, 'Cannot toggle status of this user.');

        $newStatus = $user->status === 'active' ? 'inactive' : 'active';
        $user->update(['status' => $newStatus]);

        return back()->with('success', "User has been " . ($newStatus === 'active' ? 'activated' : 'deactivated') . " successfully.");
    }
}
