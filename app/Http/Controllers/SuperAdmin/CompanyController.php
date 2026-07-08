<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Inertia\Inertia;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::withCount(['users', 'meetingMemos'])
            ->latest()
            ->get()
            ->map(fn($c) => [
                'id'                    => $c->id,
                'name'                  => $c->name,
                'email'                 => $c->email,
                'phone'                 => $c->phone,
                'status'                => $c->status,
                'users_count'           => $c->users_count ?? 0,
                'meeting_memos_count' => $c->meeting_memos_count ?? 0,
                'created_at'            => $c->created_at->format('M d, Y'),
            ]);

        return Inertia::render('SuperAdmin/Companies/Index', compact('companies'));
    }

    public function create()
    {
        return Inertia::render('SuperAdmin/Companies/Create');
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'nullable|email|max:255|unique:companies,email',
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'status'  => 'required|in:active,inactive',
            'has_checker' => 'boolean',
            'has_verifier' => 'boolean',
            'has_approver' => 'boolean',
        ]);

        Company::create($validated);

        return redirect()->route('super-admin.companies.index')
            ->with('success', 'Company created successfully.');
    }

    public function show(Company $company)
    {
        $company->load(['users' => fn($q) => $q->where('role', '!=', 'super_admin')->withCount('meetingMemos')]);

        $stats = [
            'total_users'           => $company->users->count(),
            'admins'                => $company->users->where('role', 'admin')->count(),
            'managers'              => $company->users->where('role', 'manager')->count(),
            'staffs'               => $company->users->where('role', 'staff')->count(),
            'total_memos'         => $company->meetingMemos()->count(),
            'pending_memos'       => $company->meetingMemos()->where('status', 'pending')->count(),
            'approved_memos'      => $company->meetingMemos()->where('status', 'approved')->count(),
        ];

        return Inertia::render('SuperAdmin/Companies/Show', [
            'company' => $company,
            'stats'   => $stats,
        ]);
    }

    public function edit(Company $company)
    {
        return Inertia::render('SuperAdmin/Companies/Edit', [
            'company' => $company,
        ]);
    }

    public function update(\Illuminate\Http\Request $request, Company $company)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'nullable|email|max:255|unique:companies,email,' . $company->id,
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'status'  => 'required|in:active,inactive',
            'has_checker' => 'boolean',
            'has_verifier' => 'boolean',
            'has_approver' => 'boolean',
        ]);

        $company->update($validated);

        return redirect()->route('super-admin.companies.index')
            ->with('success', 'Company updated successfully.');
    }

    public function destroy(Company $company)
    {
        // Deleting the company will cascade delete users if DB is set up that way, 
        // or we can manually delete the users here. Assuming models/DB cascade or we manually delete.
        $company->users()->delete();
        $company->delete();

        return redirect()->route('super-admin.companies.index')
            ->with('success', 'Company and its users have been permanently deleted.');
    }

    public function toggleStatus(Company $company)
    {
        $newStatus = $company->status === 'active' ? 'inactive' : 'active';
        $company->update(['status' => $newStatus]);

        if ($newStatus === 'inactive') {
            // Deactivate all users in the company
            $company->users()->update(['status' => 'inactive']);
        } else {
            // Activate only the company admins
            $company->users()->where('role', 'admin')->update(['status' => 'active']);
        }

        return back()->with('success', "Company {$company->name} has been " . ($newStatus === 'active' ? 'activated (Admins restored)' : 'deactivated (All users deactivated)') . '.');
    }
}
