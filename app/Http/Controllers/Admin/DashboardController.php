<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\MeetingMinute;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $companyId = auth()->user()->company_id;

        // Statistics
        $totalUsers = User::where('company_id', $companyId)->where('role', '!=', 'super_admin')->count();
        $totalManagers = User::where('company_id', $companyId)->where('role', 'manager')->count();
        $totalClients = User::where('company_id', $companyId)->where('role', 'client')->count();
        
        $totalMinutes = MeetingMinute::where('company_id', $companyId)->count();
        $pendingMinutes = MeetingMinute::where('company_id', $companyId)->where('status', 'pending')->count();
        $approvedMinutes = MeetingMinute::where('company_id', $companyId)->where('status', 'approved')->count();

        // Recent Activity
        $recentUsers = User::where('company_id', $companyId)
            ->where('role', '!=', 'super_admin')
            ->latest()
            ->take(5)
            ->get(['id', 'name', 'email', 'role', 'created_at']);

        $recentMinutes = MeetingMinute::with('creator:id,name')
            ->where('company_id', $companyId)
            ->latest()
            ->take(5)
            ->get(['id', 'title', 'status', 'created_by', 'created_at']);

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'users' => [
                    'total' => $totalUsers,
                    'managers' => $totalManagers,
                    'clients' => $totalClients,
                ],
                'minutes' => [
                    'total' => $totalMinutes,
                    'pending' => $pendingMinutes,
                    'approved' => $approvedMinutes,
                ],
            ],
            'recentUsers' => $recentUsers,
            'recentMinutes' => $recentMinutes,
            'company' => auth()->user()->company,
        ]);
    }
}
