<?php

namespace App\Http\Controllers;

use App\Exports\MemoExport;
use App\Models\Company;
use App\Models\MeetingMemo;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $role = $user->role;

        // Only super_admin, staff can access
        abort_unless(in_array($role, ['super_admin', 'staff']), 403);

        $companies = [];
        $users = [];

        if ($role === 'super_admin') {
            $companies = Company::where('status', 'active')->select('id', 'name')->get();
            $users = User::whereIn('role', ['staff', 'manager', 'admin'])
                ->select('id', 'name', 'company_id')
                ->get();
            $memocreators = User::whereIn('role', ['staff'])
                ->select('id', 'name', 'company_id')
                ->get();
            $memoverifers = User::whereIn('role', ['manager'])
                ->select('id', 'name', 'company_id')
                ->get();
            $memoapprovers = User::whereIn('role', ['admin'])
                ->select('id', 'name', 'company_id')
                ->get();
        } else {
            $companies = [];
            $users = [];
            $memocreators = [];
            $memoverifers = [];
            $memoapprovers = [];
        }

        // Determine the correct export route
        $exportUrl = $role === 'super_admin'
            ? route('super-admin.reports.export')
            : route('reports.export');

        return Inertia::render('Reports/Index', [
            'companies'     => $companies,
            'users'         => $users,
            'memocreators'  => $memocreators,
            'memoverifers'  => $memoverifers,
            'memoapprovers' => $memoapprovers,
            'isSuperAdmin'  => $role === 'super_admin',
            'exportUrl'     => $exportUrl,
        ]);
    }

    public function export(Request $request)
    {
        $user = auth()->user();
        $role = $user->role;

        abort_unless(in_array($role, ['super_admin', 'staff']), 403);

        $request->validate([
            'status'     => 'nullable|string|in:all,draft,pending,approved,rejected',
            'company_id' => 'nullable|integer|exists:companies,id',
            'date_from'  => 'nullable|date',
            'date_to'    => 'nullable|date|after_or_equal:date_from',
            'created_by'    => 'nullable|integer|exists:users,id',
            'memocreators'  => 'nullable|integer|exists:users,id',
            'memoverifers'  => 'nullable|integer|exists:users,id',
            'memoapprovers' => 'nullable|integer|exists:users,id',
        ]);

        $filters = $request->only(['status', 'company_id', 'date_from', 'date_to', 'created_by', 'memocreators', 'memoverifers', 'memoapprovers']);

        // Enforce scopes
        if ($role !== 'super_admin') {
            $filters['company_id'] = $user->company_id;
            $filters['created_by'] = $user->id;
            $filters['memocreators'] = $user->id;
            unset($filters['memoverifers']);
            unset($filters['memoapprovers']);
        }

        $filename = 'memo-report-' . now()->format('Y-m-d-His') . '.xlsx';

        return Excel::download(new MemoExport($filters), $filename);
    }
}
