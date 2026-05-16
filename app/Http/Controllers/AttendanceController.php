<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function clockIn(Request $request)
    {
        $request->validate([
            'lat'     => 'nullable|numeric|between:-90,90',
            'lng'     => 'nullable|numeric|between:-180,180',
            'address' => 'nullable|string|max:500',
        ]);

        $user  = auth()->user();
        $today = Carbon::today()->toDateString();

        if (Attendance::where('user_id', $user->id)->where('date', $today)->exists()) {
            return back()->with('error', 'You have already clocked in today.');
        }

        Attendance::create([
            'user_id'         => $user->id,
            'company_id'      => $user->company_id,
            'date'            => $today,
            'clock_in'        => Carbon::now(),
            'clock_in_lat'    => $request->lat,
            'clock_in_lng'    => $request->lng,
            'clock_in_address'=> $request->address,
        ]);

        return back()->with('success', 'Clocked in at ' . Carbon::now()->format('g:i A') . '. Have a great day!');
    }

    public function clockOut(Request $request)
    {
        $request->validate([
            'lat'     => 'nullable|numeric|between:-90,90',
            'lng'     => 'nullable|numeric|between:-180,180',
            'address' => 'nullable|string|max:500',
        ]);

        $user   = auth()->user();
        $today  = Carbon::today()->toDateString();
        $record = Attendance::where('user_id', $user->id)->where('date', $today)->first();

        if (!$record) {
            return back()->with('error', 'You have not clocked in today.');
        }

        if ($record->clock_out) {
            return back()->with('error', 'You have already clocked out today.');
        }

        $record->update([
            'clock_out'         => Carbon::now(),
            'clock_out_lat'     => $request->lat,
            'clock_out_lng'     => $request->lng,
            'clock_out_address' => $request->address,
        ]);

        return back()->with('success', 'Clocked out at ' . Carbon::now()->format('g:i A') . '. See you tomorrow!');
    }
}
