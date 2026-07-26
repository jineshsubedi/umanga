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
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $query = User::where('company_id', $companyId)
            ->where('role', '!=', 'super_admin')
            ->withCount('meetingMemos')
            ->with('department');

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('department')) {
            $query->where('department_id', $request->department);
        }

        $users = $query->latest()->get();

        $departments = \App\Models\Department::where('company_id', $companyId)
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Admin/Users/Index', [
            'users'       => $users,
            'filters'     => $request->only(['search', 'role', 'status', 'department']),
            'departments' => $departments,
        ]);
    }

    public function create()
    {
        $company = auth()->user()->company;
        return Inertia::render('Admin/Users/Create', compact('company'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|unique:users',
            'role'        => 'required|in:admin,manager,staff',
            'designation' => 'nullable|string|max:255',
            'password'    => ['required', Rules\Password::defaults()],
            'is_checker'  => 'boolean',
            'is_verifier' => 'boolean',
            'is_approver' => 'boolean',
            'department'  => 'nullable|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'in:memo,procurement',
        ]);

        $user = User::create([
            'company_id'  => auth()->user()->company_id,
            'name'        => $request->name,
            'email'       => $request->email,
            'role'        => $request->role,
            'designation' => $request->designation,
            'password'    => Hash::make($request->password),
            'status'      => 'active',
            'is_checker'  => $request->is_checker ?? false,
            'is_verifier' => $request->is_verifier ?? false,
            'is_approver' => $request->is_approver ?? false,
            'department'  => $request->department,
        ]);

        if ($request->has('permissions')) {
            $company = auth()->user()->company;
            $companyModules = $company->modules ?? [];
            foreach ($request->permissions as $perm) {
                if (in_array($perm, $companyModules)) {
                    $user->modulePermissions()->create(['module_name' => $perm]);
                }
            }
        }

        event(new Registered($user));

        return redirect()->route('admin.users.index')->with('success', 'User created successfully. Verification email sent.');
    }

    public function edit(User $user)
    {
        abort_if($user->company_id !== auth()->user()->company_id, 403);
        $company = auth()->user()->company;
        return Inertia::render('Admin/Users/Edit', [
            'user' => $user,
            'company' => $company,
            'permissions' => $user->modulePermissions->pluck('module_name')->toArray()
        ]);
    }

    public function update(Request $request, User $user)
    {
        abort_if($user->company_id !== auth()->user()->company_id, 403);

        $request->validate([
            'name'        => 'required|string|max:255',
            'role'        => 'required|in:admin,manager,staff',
            'designation' => 'nullable|string|max:255',
            'status'      => 'required|in:active,inactive',
            'is_checker'  => 'boolean',
            'is_verifier' => 'boolean',
            'is_approver' => 'boolean',
            'department'  => 'nullable|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'in:memo,procurement',
        ]);

        $user->update($request->only('name', 'role', 'designation', 'status', 'is_checker', 'is_verifier', 'is_approver', 'department'));

        $user->modulePermissions()->delete();
        if ($request->has('permissions')) {
            $company = auth()->user()->company;
            $companyModules = $company->modules ?? [];
            foreach ($request->permissions as $perm) {
                if (in_array($perm, $companyModules)) {
                    $user->modulePermissions()->create(['module_name' => $perm]);
                }
            }
        }

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
