<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\MeetingMemo;
use App\Models\MeetingMemoReview;
use App\Notifications\MemoReviewed;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MeetingMemoController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');
        $companyId = auth()->user()->company_id;
        $managerId = auth()->id();

        $memos = MeetingMemo::with('creator:id,name,email')
            ->where('company_id', $companyId)
            ->whereHas('managers', function ($q) use ($managerId) {
                $q->where('manager_id', $managerId);
            })
            ->where('status', $status)
            ->latest()
            ->get();

        $counts = [
            'pending'  => MeetingMemo::where('company_id', $companyId)->whereHas('managers', fn($q) => $q->where('manager_id', $managerId))->where('status', 'pending')->count(),
            'approved' => MeetingMemo::where('company_id', $companyId)->whereHas('managers', fn($q) => $q->where('manager_id', $managerId))->where('status', 'approved')->count(),
            'rejected' => MeetingMemo::where('company_id', $companyId)->whereHas('managers', fn($q) => $q->where('manager_id', $managerId))->where('status', 'rejected')->count(),
        ];

        return Inertia::render('Manager/MeetingMemos/Index', compact('memos', 'counts', 'status'));
    }

    public function show(MeetingMemo $meetingMemo)
    {
        abort_if($meetingMemo->company_id !== auth()->user()->company_id, 403);
        abort_if(!$meetingMemo->managers()->where('manager_id', auth()->id())->exists(), 403, 'You are not assigned to review this memo.');
        $meetingMemo->load(['creator:id,name,email', 'reviews.reviewer:id,name', 'attachments']);

        return Inertia::render('Manager/MeetingMemos/Show', ['memo' => $meetingMemo]);
    }

    public function review(Request $request, MeetingMemo $meetingMemo)
    {
        abort_if($meetingMemo->company_id !== auth()->user()->company_id, 403);
        abort_if(!$meetingMemo->managers()->where('manager_id', auth()->id())->exists(), 403, 'You are not assigned to review this memo.');
        abort_if($meetingMemo->status !== 'pending', 422, 'This memo is not pending review.');

        $request->validate([
            'status'  => 'required|in:approved,rejected',
            'comment' => 'required_if:status,rejected|nullable|string|max:1000',
        ]);

        MeetingMemoReview::create([
            'meeting_memo_id' => $meetingMemo->id,
            'reviewed_by'       => auth()->id(),
            'status'            => $request->status,
            'comment'           => $request->comment,
        ]);

        $meetingMemo->update(['status' => $request->status]);

        $meetingMemo->creator->notify(new MemoReviewed(
            $meetingMemo,
            $request->status,
            $request->comment,
            auth()->user()->name
        ));

        return redirect()->route('manager.meeting-memos.index')
            ->with('success', ' memo ' . $request->status . ' successfully.');
    }
}
