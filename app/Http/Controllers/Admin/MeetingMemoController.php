<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MeetingMemo;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MeetingMemoController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'all');

        $query = MeetingMemo::where('company_id', auth()->user()->company_id)
            ->where('status', '!=', 'draft')
            ->with(['creator:id,name', 'latestReview.reviewer:id,name']);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $memos = $query->latest()->get();

        $counts = [
            'all'      => MeetingMemo::where('company_id', auth()->user()->company_id)->where('status', '!=', 'draft')->count(),
            'pending'  => MeetingMemo::where('company_id', auth()->user()->company_id)->where('status', 'pending')->count(),
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
}
