<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $companyId = auth()->user()->company_id;

        // Get attendances grouped by date and role
        $attendances = DB::table('attendances')
            ->join('users', 'attendances.user_id', '=', 'users.id')
            ->where('users.company_id', $companyId)
            ->selectRaw('attendances.date, users.role, count(*) as count')
            ->groupBy('attendances.date', 'users.role')
            ->get();

        // Get memos grouped by date (exclude draft)
        $memos = DB::table('meeting_memos')
            ->where('company_id', $companyId)
            ->where('status', '!=', 'draft')
            ->selectRaw('DATE(meeting_date) as date, count(*) as count')
            ->groupBy(DB::raw('DATE(meeting_date)'))
            ->get();

        // Total active users by role to calculate absentees
        $totalUsersByRole = DB::table('users')
            ->where('company_id', $companyId)
            ->where('status', 'active')
            ->selectRaw('role, count(*) as count')
            ->groupBy('role')
            ->get()->keyBy('role');

        return Inertia::render('Admin/Calendar/Index', [
            'attendances' => $attendances,
            'memos' => $memos,
            'totalUsersByRole' => $totalUsersByRole,
        ]);
    }
}
