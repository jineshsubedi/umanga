<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        $attendances = Attendance::where('user_id', $user->id)->get();
        $meetingMemos = \App\Models\MeetingMemo::whereHas('managers', function($q) use($user) {
                $q->where('manager_id', $user->id);
            })
            ->where('status', '!=', 'draft')
            ->select('id', 'title', 'meeting_date', 'status')
            ->get();
            
        return Inertia::render('Manager/Attendance/Index', [
            'attendances' => $attendances,
            'meetingMemos' => $meetingMemos,
        ]);
    }
}
