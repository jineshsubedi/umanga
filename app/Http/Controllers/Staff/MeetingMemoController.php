<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\MeetingMemo;
use App\Models\User;
use App\Notifications\MemoSubmitted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;

class MeetingMemoController extends Controller
{
    public function index()
    {
        $memos = MeetingMemo::where('created_by', auth()->id())
            ->with(['latestReview.reviewer:id,name', 'managers:id,name'])
            ->latest()
            ->get();

        return Inertia::render('Staff/MeetingMemos/Index', compact('memos'));
    }

    public function create()
    {
        $managers = User::where('company_id', auth()->user()->company_id)
            ->where('role', 'manager')
            ->where('status', 'active')
            ->select('id', 'name')
            ->get();

        return Inertia::render('Staff/MeetingMemos/Create', compact('managers'));
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

        $meetingMemo = MeetingMemo::create([
            'company_id'   => auth()->user()->company_id,
            'created_by'   => auth()->id(),
            'title'        => $request->title,
            'content'      => $request->content,
            'meeting_date' => $request->meeting_date,
            'status'       => 'draft',
        ]);

        $meetingMemo->managers()->sync($request->manager_ids);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('attachments', 'public');
                $meetingMemo->attachments()->create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getClientOriginalExtension() ?: $file->guessExtension(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        return redirect()->route('staff.meeting-memos.index')
            ->with('success', ' memo created successfully.');
    }

    public function show(MeetingMemo $meetingMemo)
    {
        abort_if($meetingMemo->created_by !== auth()->id(), 403);
        $meetingMemo->load(['reviews.reviewer:id,name', 'attachments', 'managers:id,name']);

        return Inertia::render('Staff/MeetingMemos/Show', ['memo' => $meetingMemo]);
    }

    public function edit(MeetingMemo $meetingMemo)
    {
        abort_if($meetingMemo->created_by !== auth()->id(), 403);
        abort_if($meetingMemo->status !== 'draft', 422, 'Cannot edit this memo.');
        $meetingMemo->load(['latestReview', 'attachments', 'managers:id,name']);

        $managers = User::where('company_id', auth()->user()->company_id)
            ->where('role', 'manager')
            ->where('status', 'active')
            ->select('id', 'name')
            ->get();

        return Inertia::render('Staff/MeetingMemos/Edit', [
            'memo' => $meetingMemo,
            'managers' => $managers,
        ]);
    }

    public function update(Request $request, MeetingMemo $meetingMemo)
    {
        abort_if($meetingMemo->created_by !== auth()->id(), 403);
        abort_if($meetingMemo->status !== 'draft', 422, 'Cannot edit this memo.');

        $request->validate([
            'title'        => 'required|string|max:255',
            'content'      => 'required|string',
            'meeting_date' => 'required|date',
            'manager_ids'  => 'required|array|min:1',
            'manager_ids.*'=> 'exists:users,id',
            'attachments.*'=> 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,ppt,pptx,txt|max:10240',
        ]);

        $meetingMemo->update($request->only('title', 'content', 'meeting_date'));
        $meetingMemo->managers()->sync($request->manager_ids);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('attachments', 'public');
                $meetingMemo->attachments()->create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getClientOriginalExtension() ?: $file->guessExtension(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        return redirect()->route('staff.meeting-memos.index')
            ->with('success', ' memo updated successfully.');
    }

    public function submit(MeetingMemo $meetingMemo)
    {
        abort_if($meetingMemo->created_by !== auth()->id(), 403);
        abort_if($meetingMemo->status !== 'draft', 422, 'Only draft memos can be submitted.');

        $meetingMemo->update(['status' => 'pending_manager']);

        // Notify assigned managers
        $managers = $meetingMemo->managers()->where('status', 'active')->get();
            
        Notification::send($managers, new MemoSubmitted($meetingMemo));

        return back()->with('success', ' memo submitted for manager approval.');
    }

    public function destroy(MeetingMemo $meetingMemo)
    {
        abort_if($meetingMemo->created_by !== auth()->id(), 403);
        abort_if($meetingMemo->status === 'approved', 422, 'Cannot delete an approved memo.');

        $meetingMemo->delete();

        return back()->with('success', ' memo deleted.');
    }

    public function duplicate(MeetingMemo $meetingMemo)
    {
        abort_if($meetingMemo->created_by !== auth()->id(), 403);
        abort_if($meetingMemo->status !== 'rejected', 422, 'Only rejected memos can be duplicated.');

        $newMemo = $meetingMemo->replicate();
        $newMemo->status = 'draft';
        $newMemo->title = $newMemo->title . ' (Revision)';
        $newMemo->save();

        $newMemo->managers()->sync($meetingMemo->managers->pluck('id'));

        foreach ($meetingMemo->attachments as $attachment) {
            $newMemo->attachments()->create([
                'file_name' => $attachment->file_name,
                'file_path' => $attachment->file_path,
                'file_type' => $attachment->file_type,
                'file_size' => $attachment->file_size,
            ]);
        }

        return redirect()->route('staff.meeting-memos.edit', $newMemo->id)
            ->with('success', ' memo duplicated as a new draft. You can now revise it.');
    }
}
