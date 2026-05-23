<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use App\Models\MeetingMemo;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Carbon\Carbon;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $stats = [
            'total_companies'    => Company::count(),
            'active_companies'   => Company::where('status', 'active')->count(),
            'inactive_companies' => Company::where('status', 'inactive')->count(),
            'total_users'        => User::where('role', '!=', 'super_admin')->count(),
            'active_users'       => User::where('status', 'active')->where('role', '!=', 'super_admin')->count(),
            'admins_count'       => User::where('role', 'admin')->where('status', 'active')->count(),
            'managers_count'     => User::where('role', 'manager')->where('status', 'active')->count(),
            'staffs_count'      => User::where('role', 'staff')->where('status', 'active')->count(),
            'total_memos'      => MeetingMemo::count(),
            'present_today'      => \App\Models\Attendance::whereDate('date', Carbon::today())->count(),
            'clocked_in_now'     => \App\Models\Attendance::whereDate('date', Carbon::today())->whereNotNull('clock_in')->whereNull('clock_out')->count(),
            'completed_today'    => \App\Models\Attendance::whereDate('date', Carbon::today())->whereNotNull('clock_out')->count(),
        ];

        // Graph Data: Memos created per month over the last 6 months
        $months = [];
        $memosData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->format('M');
            $memosData[] = MeetingMemo::whereMonth('created_at', $date->month)
                                          ->whereYear('created_at', $date->year)
                                          ->count();
        }

        // Graph Data: Companies registered per month over the last 6 months
        $companiesData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $companiesData[] = Company::whereMonth('created_at', $date->month)
                                      ->whereYear('created_at', $date->year)
                                      ->count();
        }

        // Filters for Daily Memos Chart
        $selectedYear = $request->input('year', Carbon::now()->year);
        $selectedMonth = $request->input('month', Carbon::now()->month);

        $targetDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1);
        $daysInMonth = $targetDate->daysInMonth;

        $daysLabels = [];
        $dayCreated = [];
        $dayApproved = [];
        $dayRejected = [];

        for ($d = 1; $d <= $daysInMonth; $d++) {
            $daysLabels[] = $d;
            $dayCreated[] = MeetingMemo::whereYear('created_at', $selectedYear)
                                         ->whereMonth('created_at', $selectedMonth)
                                         ->whereDay('created_at', $d)
                                         ->count();
            $dayApproved[] = MeetingMemo::whereYear('created_at', $selectedYear)
                                          ->whereMonth('created_at', $selectedMonth)
                                          ->whereDay('created_at', $d)
                                          ->where('status', 'approved')
                                          ->count();
            $dayRejected[] = MeetingMemo::whereYear('created_at', $selectedYear)
                                          ->whereMonth('created_at', $selectedMonth)
                                          ->whereDay('created_at', $d)
                                          ->where('status', 'rejected')
                                          ->count();
        }

        $chartData = [
            'labels' => $months,
            'memos' => $memosData,
            'companies' => $companiesData,
            'max_y' => max(10, max($memosData) + 5, max($companiesData) + 5),
            'days_labels' => $daysLabels,
            'day_created' => $dayCreated,
            'day_approved' => $dayApproved,
            'day_rejected' => $dayRejected,
            'max_status_y' => max(5, max($dayCreated) + 2),
        ];

        $earliestYear = MeetingMemo::min(DB::raw('YEAR(created_at)')) ?? Carbon::now()->year;
        $availableYears = range($earliestYear, Carbon::now()->year + 1);

        return Inertia::render('SuperAdmin/Dashboard', [
            'stats' => $stats,
            'chartData' => $chartData,
            'filters' => [
                'year' => (int) $selectedYear,
                'month' => (int) $selectedMonth,
            ],
            'availableYears' => $availableYears
        ]);
    }
}
