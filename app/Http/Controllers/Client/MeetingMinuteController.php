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
            ->with(['latestReview.reviewer:id,name', 'managers:id,name'])
            ->latest()
            ->get();

        return Inertia::render('Client/MeetingMinutes/Index', compact('minutes'));
    }

    public function create()
    {
        $managers = User::where('company_id', auth()->user()->company_id)
            ->where('role', 'manager')
            ->where('status', 'active')
            ->select('id', 'name')
            ->get();

        return Inertia::render('Client/MeetingMinutes/Create', compact('managers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'        => 'required|string|max:255',
            'content'      => 'required|string',
            'meeting_date' => 'required|date',
            'manager_ids'  => 'required|array|min:1',
            'manager_ids.*'=> 'exists:users,id',
            'attachments.*'=> 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,ppt,pptx,txt|max:10240',
        ]);

        $meetingMinute = MeetingMinute::create([
            'company_id'   => auth()->user()->company_id,
            'created_by'   => auth()->id(),
            'title'        => $request->title,
            'content'      => $request->content,
            'meeting_date' => $request->meeting_date,
            'status'       => 'draft',
        ]);

        $meetingMinute->managers()->sync($request->manager_ids);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('attachments', 'public');
                $meetingMinute->attachments()->create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getClientOriginalExtension() ?: $file->guessExtension(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        return redirect()->route('client.meeting-minutes.index')
            ->with('success', 'Meeting minute created successfully.');
    }

    public function show(MeetingMinute $meetingMinute)
    {
        abort_if($meetingMinute->created_by !== auth()->id(), 403);
        $meetingMinute->load(['reviews.reviewer:id,name', 'attachments', 'managers:id,name']);

        return Inertia::render('Client/MeetingMinutes/Show', ['minute' => $meetingMinute]);
    }

    public function edit(MeetingMinute $meetingMinute)
    {
        abort_if($meetingMinute->created_by !== auth()->id(), 403);
        abort_if($meetingMinute->status !== 'draft', 422, 'Cannot edit this minute.');
        $meetingMinute->load(['latestReview', 'attachments', 'managers:id,name']);

        $managers = User::where('company_id', auth()->user()->company_id)
            ->where('role', 'manager')
            ->where('status', 'active')
            ->select('id', 'name')
            ->get();

        return Inertia::render('Client/MeetingMinutes/Edit', [
            'minute' => $meetingMinute,
            'managers' => $managers,
        ]);
    }

    public function update(Request $request, MeetingMinute $meetingMinute)
    {
        abort_if($meetingMinute->created_by !== auth()->id(), 403);
        abort_if($meetingMinute->status !== 'draft', 422, 'Cannot edit this minute.');

        $request->validate([
            'title'        => 'required|string|max:255',
            'content'      => 'required|string',
            'meeting_date' => 'required|date',
            'manager_ids'  => 'required|array|min:1',
            'manager_ids.*'=> 'exists:users,id',
            'attachments.*'=> 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,ppt,pptx,txt|max:10240',
        ]);

        $meetingMinute->update($request->only('title', 'content', 'meeting_date'));
        $meetingMinute->managers()->sync($request->manager_ids);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('attachments', 'public');
                $meetingMinute->attachments()->create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getClientOriginalExtension() ?: $file->guessExtension(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        return redirect()->route('client.meeting-minutes.index')
            ->with('success', 'Meeting minute updated successfully.');
    }

    public function submit(MeetingMinute $meetingMinute)
    {
        abort_if($meetingMinute->created_by !== auth()->id(), 403);
        abort_if($meetingMinute->status !== 'draft', 422, 'Only draft minutes can be submitted.');

        $meetingMinute->update(['status' => 'pending']);

        // Notify assigned managers
        $managers = $meetingMinute->managers()->where('status', 'active')->get();
            
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

    public function duplicate(MeetingMinute $meetingMinute)
    {
        abort_if($meetingMinute->created_by !== auth()->id(), 403);
        abort_if($meetingMinute->status !== 'rejected', 422, 'Only rejected minutes can be duplicated.');

        $newMinute = $meetingMinute->replicate();
        $newMinute->status = 'draft';
        $newMinute->title = $newMinute->title . ' (Revision)';
        $newMinute->save();

        $newMinute->managers()->sync($meetingMinute->managers->pluck('id'));

        foreach ($meetingMinute->attachments as $attachment) {
            $newMinute->attachments()->create([
                'file_name' => $attachment->file_name,
                'file_path' => $attachment->file_path,
                'file_type' => $attachment->file_type,
                'file_size' => $attachment->file_size,
            ]);
        }

        return redirect()->route('client.meeting-minutes.edit', $newMinute->id)
            ->with('success', 'Meeting minute duplicated as a new draft. You can now revise it.');
    }
}
