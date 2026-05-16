<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MeetingMinute;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MeetingMinuteController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'all');

        $query = MeetingMinute::where('company_id', auth()->user()->company_id)
            ->with(['creator:id,name', 'latestReview.reviewer:id,name']);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $minutes = $query->latest()->get();

        $counts = [
            'all'      => MeetingMinute::where('company_id', auth()->user()->company_id)->count(),
            'draft'    => MeetingMinute::where('company_id', auth()->user()->company_id)->where('status', 'draft')->count(),
            'pending'  => MeetingMinute::where('company_id', auth()->user()->company_id)->where('status', 'pending')->count(),
            'approved' => MeetingMinute::where('company_id', auth()->user()->company_id)->where('status', 'approved')->count(),
            'rejected' => MeetingMinute::where('company_id', auth()->user()->company_id)->where('status', 'rejected')->count(),
        ];

        return Inertia::render('Admin/MeetingMinutes/Index', compact('minutes', 'counts', 'status'));
    }

    public function show(MeetingMinute $meetingMinute)
    {
        abort_if($meetingMinute->company_id !== auth()->user()->company_id, 403);
        $meetingMinute->load(['creator:id,name', 'reviews.reviewer:id,name', 'attachments']);

        return Inertia::render('Admin/MeetingMinutes/Show', ['minute' => $meetingMinute]);
    }
}
