<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\MeetingMinute;
use App\Models\User;
use App\Notifications\MinuteSubmitted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;

class MeetingMinuteController extends Controller
{
    public function index()
    {
        $minutes = MeetingMinute::where('created_by', auth()->id())
            ->with('latestReview.reviewer:id,name')
            ->latest()
            ->get();

        return Inertia::render('Client/MeetingMinutes/Index', compact('minutes'));
    }

    public function create()
    {
        return Inertia::render('Client/MeetingMinutes/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'        => 'required|string|max:255',
            'content'      => 'required|string',
            'meeting_date' => 'required|date',
        ]);

        MeetingMinute::create([
            'company_id'   => auth()->user()->company_id,
            'created_by'   => auth()->id(),
            'title'        => $request->title,
            'content'      => $request->content,
            'meeting_date' => $request->meeting_date,
            'status'       => 'draft',
        ]);

        return redirect()->route('client.meeting-minutes.index')
            ->with('success', 'Meeting minute created successfully.');
    }

    public function show(MeetingMinute $meetingMinute)
    {
        abort_if($meetingMinute->created_by !== auth()->id(), 403);
        $meetingMinute->load(['reviews.reviewer:id,name']);

        return Inertia::render('Client/MeetingMinutes/Show', ['minute' => $meetingMinute]);
    }

    public function edit(MeetingMinute $meetingMinute)
    {
        abort_if($meetingMinute->created_by !== auth()->id(), 403);
        abort_if(!in_array($meetingMinute->status, ['draft', 'rejected']), 422, 'Cannot edit this minute.');
        $meetingMinute->load('latestReview');

        return Inertia::render('Client/MeetingMinutes/Edit', ['minute' => $meetingMinute]);
    }

    public function update(Request $request, MeetingMinute $meetingMinute)
    {
        abort_if($meetingMinute->created_by !== auth()->id(), 403);
        abort_if(!in_array($meetingMinute->status, ['draft', 'rejected']), 422);

        $request->validate([
            'title'        => 'required|string|max:255',
            'content'      => 'required|string',
            'meeting_date' => 'required|date',
        ]);

        $meetingMinute->update($request->only('title', 'content', 'meeting_date'));

        return redirect()->route('client.meeting-minutes.index')
            ->with('success', 'Meeting minute updated successfully.');
    }

    public function submit(MeetingMinute $meetingMinute)
    {
        abort_if($meetingMinute->created_by !== auth()->id(), 403);
        abort_if(!in_array($meetingMinute->status, ['draft', 'rejected']), 422);

        $meetingMinute->update(['status' => 'pending']);

        // Notify managers
        $managers = User::where('company_id', $meetingMinute->company_id)
            ->where('role', 'manager')
            ->where('status', 'active')
            ->get();
            
        Notification::send($managers, new MinuteSubmitted($meetingMinute));

        return back()->with('success', 'Meeting minute submitted for manager approval.');
    }

    public function destroy(MeetingMinute $meetingMinute)
    {
        abort_if($meetingMinute->created_by !== auth()->id(), 403);
        abort_if($meetingMinute->status === 'approved', 422, 'Cannot delete an approved minute.');

        $meetingMinute->delete();

        return back()->with('success', 'Meeting minute deleted.');
    }
}
