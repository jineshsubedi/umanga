<?php

namespace App\Http\Controllers;

use App\Models\MeetingMemo;
use App\Models\ProcurementRequest;
use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HRISDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Super admin redirects to super admin dashboard
        if ($user->role === 'super_admin') {
            return redirect()->route('super-admin.dashboard');
        }

        // Determine accessible modules based on permissions.
        // For simplicity, we fetch all permissions for this user.
        $user->load('modulePermissions');
        $modulePermissions = $user->modulePermissions->pluck('module_name')->toArray();
        
        // If no explicit permissions exist, maybe give them some defaults or check roles.
        // For now, let's just use what they have, or grant all if admin.
        if ($user->role === 'admin' || $user->role === 'manager') {
            $modulePermissions = ['memo', 'procurement', 'leave']; // grant all for now as fallback
        } elseif (empty($modulePermissions)) {
            // Default staff modules
            $modulePermissions = ['memo'];
        }

        // Calculate statistics for the dashboard
        $stats = [
            'pending_memos' => 0,
            'pending_procurement' => 0,
            'today_leave' => 0, // Placeholder
            'employees' => 0,
            'companies' => 0
        ];

        $companyId = $user->company_id;

        // Memo stats
        if (in_array('memo', $modulePermissions)) {
            $stats['pending_memos'] = MeetingMemo::where('company_id', $companyId)
                ->where('status', 'pending')
                ->count();
        }

        // Procurement stats
        if (in_array('procurement', $modulePermissions)) {
            $stats['pending_procurement'] = ProcurementRequest::where('company_id', $companyId)
                ->where('status', 'Pending')
                ->count();
        }

        // HR/Admin stats
        if ($user->role === 'admin') {
            $stats['employees'] = User::where('company_id', $companyId)->count();
            $stats['companies'] = Company::count(); // Usually admins manage one company, but if multi-tenant...
        }

        return Inertia::render('Dashboard/Index', [
            'modules' => $modulePermissions,
            'stats' => $stats,
        ]);
    }
}
