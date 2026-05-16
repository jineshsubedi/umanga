<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use App\Models\MeetingMinute;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_companies' => Company::count(),
            'active_companies'=> Company::where('status', 'active')->count(),
            'total_users'     => User::count(),
            'total_minutes'   => MeetingMinute::count(),
        ];

        // Graph Data: Minutes created per month over the last 6 months
        $months = [];
        $minutesData = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->format('M');
            $minutesData[] = MeetingMinute::whereMonth('created_at', $date->month)
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

        $chartData = [
            'labels' => $months,
            'minutes' => $minutesData,
            'companies' => $companiesData,
            'max_y' => max(10, max($minutesData) + 5, max($companiesData) + 5) // For scaling the CSS bars
        ];

        return Inertia::render('SuperAdmin/Dashboard', [
            'stats' => $stats,
            'chartData' => $chartData
        ]);
    }
}
