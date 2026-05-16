<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;

        $query = Attendance::where('company_id', $companyId)
            ->with('user:id,name,role')
            ->orderBy('date', 'desc')
            ->orderBy('clock_in', 'desc');

        // Optional date filter
        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        $attendances = $query->get();

        // Today's summary stats for the company
        $today = Carbon::today()->toDateString();
        $stats = [
            'total_today'    => Attendance::where('company_id', $companyId)->whereDate('date', $today)->count(),
            'clocked_in'     => Attendance::where('company_id', $companyId)->whereDate('date', $today)->whereNotNull('clock_in')->whereNull('clock_out')->count(),
            'clocked_out'    => Attendance::where('company_id', $companyId)->whereDate('date', $today)->whereNotNull('clock_out')->count(),
            'absent_today'   => \App\Models\User::where('company_id', $companyId)
                ->where('status', 'active')
                ->whereNotIn('role', ['super_admin', 'admin'])
                ->whereDoesntHave('attendances', fn($q) => $q->whereDate('date', $today))
                ->count(),
        ];

        return Inertia::render('Admin/Attendance/Index', [
            'attendances'  => $attendances,
            'stats'        => $stats,
            'filterDate'   => $request->date,
        ]);
    }
}
