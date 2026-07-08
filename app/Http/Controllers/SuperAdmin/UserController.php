<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('company:id,name')
            ->where('role', '!=', 'super_admin')
            ->withCount('meetingMemos')
            ->latest();

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->get();
        $companies = Company::select('id', 'name')->orderBy('name')->get();

        return Inertia::render('SuperAdmin/Users/Index', [
            'users'     => $users,
            'companies' => $companies,
            'filters'   => $request->only('company_id', 'role', 'status', 'search'),
        ]);
    }

    public function create()
    {
        $companies = Company::where('status', 'active')->orderBy('name')->get();
        return Inertia::render('SuperAdmin/Users/Create', compact('companies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|max:255|unique:users,email',
            'company_id' => 'required|exists:companies,id',
            'role'       => 'required|in:admin,manager,staff',
            'designation'=> 'nullable|string|max:255',
            'is_checker' => 'boolean',
            'is_verifier'=> 'boolean',
            'is_approver'=> 'boolean',
            'department' => 'nullable|string|max:255',
        ]);

        $validated['password'] = \Illuminate\Support\Facades\Hash::make('password');
        $validated['status'] = 'active';

        $user = User::create($validated);

        event(new \Illuminate\Auth\Events\Registered($user));

        return redirect()->route('super-admin.users.index')
            ->with('success', 'User created successfully.');
    }

    public function bulkCreate()
    {
        $companies = Company::where('status', 'active')->orderBy('name')->get();
        return Inertia::render('SuperAdmin/Users/BulkCreate', compact('companies'));
    }

    public function bulkStore(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'file'       => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('file');
        $csvData = file_get_contents($file);
        $rows = array_map('str_getcsv', explode("\n", trim($csvData)));
        $header = array_shift($rows);

        $successCount = 0;
        $errors = [];

        foreach ($rows as $index => $row) {
            if (count($row) < 3) continue;

            $name = trim($row[0]);
            $email = trim($row[1]);
            $role = strtolower(trim($row[2]));

            $validator = \Illuminate\Support\Facades\Validator::make([
                'name' => $name,
                'email' => $email,
                'role' => $role,
            ], [
                'name'  => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email',
                'role'  => 'required|in:admin,manager,staff',
            ]);

            if ($validator->fails()) {
                $errors[] = "Row " . ($index + 2) . ": " . implode(", ", $validator->errors()->all());
                continue;
            }

            $user = User::create([
                'name'       => $name,
                'email'      => $email,
                'role'       => $role,
                'company_id' => $request->company_id,
                'password'   => \Illuminate\Support\Facades\Hash::make('password'),
                'status'     => 'active',
            ]);

            event(new \Illuminate\Auth\Events\Registered($user));

            $successCount++;
        }

        if (count($errors) > 0) {
            return redirect()->route('super-admin.users.index')
                ->with('success', "{$successCount} users created successfully.")
                ->with('warning', implode('<br>', $errors));
        }

        return redirect()->route('super-admin.users.index')
            ->with('success', "{$successCount} users created successfully.");
    }

    public function edit(User $user)
    {
        abort_if($user->role === 'super_admin', 403);
        $companies = Company::where('status', 'active')->orderBy('name')->get();
        return Inertia::render('SuperAdmin/Users/Edit', compact('user', 'companies'));
    }

    public function update(Request $request, User $user)
    {
        abort_if($user->role === 'super_admin', 403);

        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'email'      => 'required|email|max:255|unique:users,email,' . $user->id,
            'company_id' => 'required|exists:companies,id',
            'role'       => 'required|in:admin,manager,staff',
            'designation'=> 'nullable|string|max:255',
            'status'     => 'required|in:active,inactive',
            'is_checker' => 'boolean',
            'is_verifier'=> 'boolean',
            'is_approver'=> 'boolean',
            'department' => 'nullable|string|max:255',
        ]);

        $user->update($validated);

        return redirect()->route('super-admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        abort_if($user->role === 'super_admin', 403);
        
        $user->delete();

        return redirect()->route('super-admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    public function toggleStatus(User $user)
    {
        abort_if($user->role === 'super_admin', 403);
        $user->update([
            'status' => $user->status === 'active' ? 'inactive' : 'active'
        ]);
        return back()->with('success', "User status updated successfully.");
    }
}
