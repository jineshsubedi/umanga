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

        $targetDate = $request->filled('date') ? Carbon::parse($request->date)->toDateString() : Carbon::today()->toDateString();

        $absentees = \App\Models\User::where('company_id', $companyId)
            ->where('status', 'active')
            ->whereNotIn('role', ['super_admin', 'admin'])
            ->whereDoesntHave('attendances', fn($q) => $q->whereDate('date', $targetDate))
            ->select('id', 'name', 'role')
            ->get();

        // Summary stats for the company
        $stats = [
            'total_today'    => Attendance::where('company_id', $companyId)->whereDate('date', $targetDate)->count(),
            'clocked_in'     => Attendance::where('company_id', $companyId)->whereDate('date', $targetDate)->whereNotNull('clock_in')->whereNull('clock_out')->count(),
            'clocked_out'    => Attendance::where('company_id', $companyId)->whereDate('date', $targetDate)->whereNotNull('clock_out')->count(),
            'absent_today'   => $absentees->count(),
        ];

        return Inertia::render('Admin/Attendance/Index', [
            'attendances'  => $attendances,
            'stats'        => $stats,
            'absentees'    => $absentees,
            'filterDate'   => $request->date,
        ]);
    }
}
