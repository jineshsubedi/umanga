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
        $status = $request->get('status', 'pending_manager');
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
            'pending'  => MeetingMemo::where('company_id', $companyId)->whereHas('managers', fn($q) => $q->where('manager_id', $managerId))->where('status', 'pending_manager')->count(),
            'approved' => MeetingMemo::where('company_id', $companyId)->whereHas('managers', fn($q) => $q->where('manager_id', $managerId))->whereIn('status', ['pending_admin', 'approved'])->count(),
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
        abort_if($meetingMemo->status !== 'pending_manager', 422, 'This memo is not pending your review.');

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

        $newStatus = $request->status === 'approved' ? 'pending_admin' : 'rejected';
        $meetingMemo->update(['status' => $newStatus]);

        $meetingMemo->creator->notify(new MemoReviewed(
            $meetingMemo,
            $request->status,
            $request->comment,
            auth()->user()->name
        ));

        return redirect()->route('manager.meeting-memos.index')
            ->with('success', ' memo ' . $request->status . ' successfully.');
    }

    public function downloadPdf(MeetingMemo $meetingMemo)
    {
        abort_if($meetingMemo->company_id !== auth()->user()->company_id, 403);
        abort_if(!$meetingMemo->managers()->where('manager_id', auth()->id())->exists(), 403, 'You are not assigned to this memo.');
        
        $meetingMemo->load(['creator', 'company', 'reviews.reviewer']);
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.memo', ['memo' => $meetingMemo]);
        return $pdf->download('memo-' . $meetingMemo->id . '.pdf');
    }
}
