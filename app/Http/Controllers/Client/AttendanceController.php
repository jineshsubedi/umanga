<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        $attendances = Attendance::where('user_id', $user->id)->get();
        $meetingMinutes = \App\Models\MeetingMinute::where('created_by', $user->id)
            ->select('id', 'title', 'meeting_date', 'status')
            ->get();
            
        return Inertia::render('Client/Attendance/Index', [
            'attendances' => $attendances,
            'meetingMinutes' => $meetingMinutes,
        ]);
    }
}
