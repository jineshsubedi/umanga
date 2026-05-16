<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Inertia\Inertia;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::withCount(['users', 'meetingMinutes'])
            ->latest()
            ->get()
            ->map(fn($c) => [
                'id'                    => $c->id,
                'name'                  => $c->name,
                'email'                 => $c->email,
                'phone'                 => $c->phone,
                'status'                => $c->status,
                'users_count'           => $c->users_count ?? 0,
                'meeting_minutes_count' => $c->meeting_minutes_count ?? 0,
                'created_at'            => $c->created_at->format('M d, Y'),
            ]);

        return Inertia::render('SuperAdmin/Companies/Index', compact('companies'));
    }

    public function show(Company $company)
    {
        $company->load(['users' => fn($q) => $q->where('role', '!=', 'super_admin')->withCount('meetingMinutes')]);

        $stats = [
            'total_users'           => $company->users->count(),
            'admins'                => $company->users->where('role', 'admin')->count(),
            'managers'              => $company->users->where('role', 'manager')->count(),
            'clients'               => $company->users->where('role', 'client')->count(),
            'total_minutes'         => $company->meetingMinutes()->count(),
            'pending_minutes'       => $company->meetingMinutes()->where('status', 'pending')->count(),
            'approved_minutes'      => $company->meetingMinutes()->where('status', 'approved')->count(),
        ];

        return Inertia::render('SuperAdmin/Companies/Show', [
            'company' => $company,
            'stats'   => $stats,
        ]);
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
