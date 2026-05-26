<?php

namespace App\Http\Controllers\Admin;

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
        $status = $request->query('status', 'pending_admin');

        $query = MeetingMemo::where('company_id', auth()->user()->company_id)
            ->where('status', '!=', 'draft')
            ->with(['creator:id,name', 'latestReview.reviewer:id,name']);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $memos = $query->latest()->get();

        $counts = [
            'all'      => MeetingMemo::where('company_id', auth()->user()->company_id)->where('status', '!=', 'draft')->count(),
            'pending'  => MeetingMemo::where('company_id', auth()->user()->company_id)->where('status', 'pending_admin')->count(),
            'approved' => MeetingMemo::where('company_id', auth()->user()->company_id)->where('status', 'approved')->count(),
            'rejected' => MeetingMemo::where('company_id', auth()->user()->company_id)->where('status', 'rejected')->count(),
        ];

        return Inertia::render('Admin/MeetingMemos/Index', compact('memos', 'counts', 'status'));
    }

    public function show(MeetingMemo $meetingMemo)
    {
        abort_if($meetingMemo->company_id !== auth()->user()->company_id, 403);
        $meetingMemo->load(['creator:id,name', 'reviews.reviewer:id,name', 'attachments']);

        return Inertia::render('Admin/MeetingMemos/Show', ['memo' => $meetingMemo]);
    }

    public function review(Request $request, MeetingMemo $meetingMemo)
    {
        abort_if($meetingMemo->company_id !== auth()->user()->company_id, 403);
        abort_if($meetingMemo->status !== 'pending_admin', 422, 'This memo is not pending your review.');

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

        return redirect()->route('admin.meeting-memos.index')
            ->with('success', ' memo ' . $request->status . ' successfully.');
    }

    public function downloadPdf(MeetingMemo $meetingMemo)
    {
        abort_if($meetingMemo->company_id !== auth()->user()->company_id, 403);
        if ($meetingMemo->status !== 'approved') {
            abort(403, 'Only approved memos can be downloaded.');
        }
        
        $meetingMemo->load(['creator', 'company', 'reviews.reviewer']);
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.memo', ['memo' => $meetingMemo]);
        return $pdf->download('memo-' . $meetingMemo->id . '.pdf');
    }
}
