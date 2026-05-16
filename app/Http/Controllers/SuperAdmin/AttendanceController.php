<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Company;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::with(['user:id,name,role,company_id', 'user.company:id,name'])
            ->orderBy('date', 'desc')
            ->orderBy('clock_in', 'desc');

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        $attendances = $query->get();

        $today = Carbon::today()->toDateString();

        $stats = [
            'total_today'   => Attendance::whereDate('date', $today)->count(),
            'clocked_in'    => Attendance::whereDate('date', $today)->whereNotNull('clock_in')->whereNull('clock_out')->count(),
            'clocked_out'   => Attendance::whereDate('date', $today)->whereNotNull('clock_out')->count(),
            'total_users'   => User::where('status', 'active')->whereNotIn('role', ['super_admin'])->count(),
        ];

        $companies = Company::select('id', 'name')->orderBy('name')->get();

        return Inertia::render('SuperAdmin/Attendance/Index', [
            'attendances'      => $attendances,
            'stats'            => $stats,
            'companies'        => $companies,
            'filterDate'       => $request->date,
            'filterCompanyId'  => $request->company_id,
        ]);
    }
}
