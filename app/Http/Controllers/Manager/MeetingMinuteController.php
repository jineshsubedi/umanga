<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\MeetingMinute;
use App\Models\MeetingMinuteReview;
use App\Notifications\MinuteReviewed;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MeetingMinuteController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');
        $companyId = auth()->user()->company_id;

        $minutes = MeetingMinute::with('creator:id,name,email')
            ->where('company_id', $companyId)
            ->where('status', $status)
            ->latest()
            ->get();

        $counts = [
            'pending'  => MeetingMinute::where('company_id', $companyId)->where('status', 'pending')->count(),
            'approved' => MeetingMinute::where('company_id', $companyId)->where('status', 'approved')->count(),
            'rejected' => MeetingMinute::where('company_id', $companyId)->where('status', 'rejected')->count(),
        ];

        return Inertia::render('Manager/MeetingMinutes/Index', compact('minutes', 'counts', 'status'));
    }

    public function show(MeetingMinute $meetingMinute)
    {
        abort_if($meetingMinute->company_id !== auth()->user()->company_id, 403);
        $meetingMinute->load(['creator:id,name,email', 'reviews.reviewer:id,name', 'attachments']);

        return Inertia::render('Manager/MeetingMinutes/Show', ['minute' => $meetingMinute]);
    }

    public function review(Request $request, MeetingMinute $meetingMinute)
    {
        abort_if($meetingMinute->company_id !== auth()->user()->company_id, 403);
        abort_if($meetingMinute->status !== 'pending', 422, 'This minute is not pending review.');

        $request->validate([
            'status'  => 'required|in:approved,rejected',
            'comment' => 'required_if:status,rejected|nullable|string|max:1000',
        ]);

        MeetingMinuteReview::create([
            'meeting_minute_id' => $meetingMinute->id,
            'reviewed_by'       => auth()->id(),
            'status'            => $request->status,
            'comment'           => $request->comment,
        ]);

        $meetingMinute->update(['status' => $request->status]);

        $meetingMinute->creator->notify(new MinuteReviewed(
            $meetingMinute,
            $request->status,
            $request->comment,
            auth()->user()->name
        ));

        return redirect()->route('manager.meeting-minutes.index')
            ->with('success', 'Meeting minute ' . $request->status . ' successfully.');
    }
}
