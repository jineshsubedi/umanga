<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\MeetingMemo;
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
        $totalStaffs = User::where('company_id', $companyId)->where('role', 'staff')->count();
        
        $totalMemos = MeetingMemo::where('company_id', $companyId)->count();
        $pendingMemos = MeetingMemo::where('company_id', $companyId)
            ->whereIn('status', ['pending_checker', 'pending_verifier', 'pending_approver'])
            ->count();
        $approvedMemos = MeetingMemo::where('company_id', $companyId)->where('status', 'approved')->count();

        // Recent Activity
        $recentUsers = User::where('company_id', $companyId)
            ->where('role', '!=', 'super_admin')
            ->latest()
            ->take(5)
            ->get(['id', 'name', 'email', 'role', 'created_at']);

        $recentMemos = MeetingMemo::with('creator:id,name')
            ->where('company_id', $companyId)
            ->latest()
            ->take(5)
            ->get(['id', 'title', 'status', 'created_by', 'created_at']);

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'users' => [
                    'total' => $totalUsers,
                    'managers' => $totalManagers,
                    'staffs' => $totalStaffs,
                ],
                'memos' => [
                    'total' => $totalMemos,
                    'pending' => $pendingMemos,
                    'approved' => $approvedMemos,
                ],
            ],
            'recentUsers' => $recentUsers,
            'recentMemos' => $recentMemos,
            'company' => auth()->user()->company,
        ]);
    }
}
