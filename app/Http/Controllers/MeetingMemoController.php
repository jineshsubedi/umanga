<?php

namespace App\Http\Controllers;

use App\Models\MeetingMemo;
use App\Models\MeetingMemoReview;
use App\Models\User;
use App\Notifications\MemoReviewed;
use App\Notifications\MemoSubmitted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;

class MeetingMemoController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'all');
        $user = auth()->user();

        // Super admins have their own controller, but just in case
        if ($user->role === 'super_admin') {
            abort(403);
        }

        $query = MeetingMemo::where('company_id', $user->company_id)
            ->with(['creator:id,name', 'latestReview.reviewer:id,name', 'checker:id,name', 'verifier:id,name', 'approver:id,name'])
            ->where(function ($q) use ($user) {
                $q->where('created_by', $user->id)
                  ->orWhere('checker_id', $user->id)
                  ->orWhere('verifier_id', $user->id)
                  ->orWhere('approver_id', $user->id)
                  ->orWhere('status', 'approved'); // Allow viewing approved memos
            });

        if ($status !== 'all') {
            if ($status === 'pending') {
                $query->whereIn('status', ['pending_checker', 'pending_verifier', 'pending_approver']);
            } else {
                $query->where('status', $status);
            }
        }

        $memos = $query->latest()->paginate(5)->withQueryString();

        // Calculate counts based on user's relation to memos
        $baseQuery = MeetingMemo::where('company_id', $user->company_id)
            ->where(function ($q) use ($user) {
                $q->where('created_by', $user->id)
                  ->orWhere('checker_id', $user->id)
                  ->orWhere('verifier_id', $user->id)
                  ->orWhere('approver_id', $user->id)
                  ->orWhere('status', 'approved');
            });

        $counts = [
            'all'      => (clone $baseQuery)->count(),
            'pending'  => (clone $baseQuery)->whereIn('status', ['pending_checker', 'pending_verifier', 'pending_approver'])->count(),
            'approved' => (clone $baseQuery)->where('status', 'approved')->count(),
            'rejected' => (clone $baseQuery)->where('status', 'rejected')->count(),
        ];

        return Inertia::render('MeetingMemos/Index', compact('memos', 'counts', 'status'));
    }

    public function create()
    {
        abort_if(auth()->user()->role !== 'staff', 403, 'Only staff members can create memos.');

        $users = User::where('company_id', auth()->user()->company_id)
            ->where('status', 'active')
            ->where('id', '!=', auth()->id())
            ->select('id', 'name', 'designation', 'role')
            ->get();

        return Inertia::render('MeetingMemos/Create', compact('users'));
    }

    public function store(Request $request)
    {
        abort_if(auth()->user()->role !== 'staff', 403, 'Only staff members can create memos.');

        $request->validate([
            'title'        => 'required|string|max:255',
            'content'      => 'required|string',
            'meeting_date' => 'required|date',
            'checker_id'   => 'required|exists:users,id',
            'verifier_id'  => 'required|exists:users,id',
            'approver_id'  => 'required|exists:users,id',
            'attachments.*'=> 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,ppt,pptx,txt|max:10240',
        ]);

        $meetingMemo = MeetingMemo::create([
            'company_id'   => auth()->user()->company_id,
            'created_by'   => auth()->id(),
            'title'        => $request->title,
            'content'      => $request->content,
            'meeting_date' => $request->meeting_date,
            'checker_id'   => $request->checker_id,
            'verifier_id'  => $request->verifier_id,
            'approver_id'  => $request->approver_id,
            'status'       => 'draft',
        ]);

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

        return redirect()->route('memos.index')->with('success', 'Memo created successfully.');
    }

    public function show(MeetingMemo $memo)
    {
        $user = auth()->user();
        abort_if($memo->company_id !== $user->company_id, 403);
        
        // Can view if creator, checker, verifier, approver, or if it's approved
        $canView = in_array($user->id, [$memo->created_by, $memo->checker_id, $memo->verifier_id, $memo->approver_id])
                   || $memo->status === 'approved';
                   
        abort_unless($canView, 403);

        $memo->load(['creator:id,name', 'reviews.reviewer:id,name', 'attachments', 'checker:id,name', 'verifier:id,name', 'approver:id,name']);

        return Inertia::render('MeetingMemos/Show', ['memo' => $memo]);
    }

    public function edit(MeetingMemo $memo)
    {
        abort_if($memo->created_by !== auth()->id(), 403);
        abort_if($memo->status !== 'draft', 422, 'Cannot edit this memo.');
        
        $memo->load(['latestReview', 'attachments', 'checker:id,name', 'verifier:id,name', 'approver:id,name']);

        $users = User::where('company_id', auth()->user()->company_id)
            ->where('status', 'active')
            ->where('id', '!=', auth()->id())
            ->select('id', 'name', 'designation', 'role')
            ->get();

        return Inertia::render('MeetingMemos/Edit', [
            'memo' => $memo,
            'users' => $users,
        ]);
    }

    public function update(Request $request, MeetingMemo $memo)
    {
        abort_if($memo->created_by !== auth()->id(), 403);
        abort_if($memo->status !== 'draft', 422, 'Cannot edit this memo.');

        $request->validate([
            'title'        => 'required|string|max:255',
            'content'      => 'required|string',
            'meeting_date' => 'required|date',
            'checker_id'   => 'required|exists:users,id',
            'verifier_id'  => 'required|exists:users,id',
            'approver_id'  => 'required|exists:users,id',
            'attachments.*'=> 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,ppt,pptx,txt|max:10240',
        ]);

        $memo->update($request->only('title', 'content', 'meeting_date', 'checker_id', 'verifier_id', 'approver_id'));

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('attachments', 'public');
                $memo->attachments()->create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_type' => $file->getClientOriginalExtension() ?: $file->guessExtension(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        return redirect()->route('memos.index')->with('success', 'Memo updated successfully.');
    }

    public function submit(MeetingMemo $memo)
    {
        abort_if($memo->created_by !== auth()->id(), 403);
        abort_if($memo->status !== 'draft', 422, 'Only draft memos can be submitted.');

        $memo->update(['status' => 'pending_checker']);

        $reviewerIds = array_unique(array_filter([$memo->checker_id, $memo->verifier_id, $memo->approver_id]));
        if (!empty($reviewerIds)) {
            $reviewers = User::whereIn('id', $reviewerIds)->get();
            foreach ($reviewers as $r) {
                $r->notify(new MemoSubmitted($memo));
            }
        }

        return back()->with('success', 'Memo submitted for checking.');
    }

    public function review(Request $request, MeetingMemo $memo)
    {
        $user = auth()->user();
        abort_if($memo->company_id !== $user->company_id, 403);
        
        $request->validate([
            'status'  => 'required|in:approved,rejected',
            'comment' => 'required_if:status,rejected|nullable|string|max:1000',
        ]);

        $newStatus = $memo->status;
        $nextReviewer = null;

        if ($memo->status === 'pending_checker' && $memo->checker_id === $user->id) {
            if ($request->status === 'approved') {
                $newStatus = 'pending_verifier';
                $nextReviewer = $memo->verifier;
            } else {
                $newStatus = 'rejected';
            }
        } elseif ($memo->status === 'pending_verifier' && $memo->verifier_id === $user->id) {
            if ($request->status === 'approved') {
                $newStatus = 'pending_approver';
                $nextReviewer = $memo->approver;
            } else {
                $newStatus = 'rejected';
            }
        } elseif ($memo->status === 'pending_approver' && $memo->approver_id === $user->id) {
            if ($request->status === 'approved') {
                $newStatus = 'approved';
            } else {
                $newStatus = 'rejected';
            }
        } else {
            abort(422, 'You are not authorized to review this memo at this stage.');
        }

        MeetingMemoReview::create([
            'meeting_memo_id' => $memo->id,
            'reviewed_by'     => $user->id,
            'status'          => $request->status,
            'comment'         => $request->comment,
        ]);

        $memo->update(['status' => $newStatus]);

        // Always notify the creator about the review
        $memo->creator->notify(new MemoReviewed(
            $memo,
            $request->status,
            $request->comment,
            $user->name
        ));

        // If approved, also notify the OTHER assigned reviewers
        if ($request->status === 'approved') {
            $otherReviewerIds = array_diff(
                array_unique(array_filter([$memo->checker_id, $memo->verifier_id, $memo->approver_id])),
                [$user->id]
            );
            if (!empty($otherReviewerIds)) {
                $otherReviewers = User::whereIn('id', $otherReviewerIds)->get();
                foreach ($otherReviewers as $r) {
                    $r->notify(new MemoReviewed(
                        $memo,
                        $request->status,
                        $request->comment,
                        $user->name
                    ));
                }
            }
        }

        return redirect()->route('memos.index')->with('success', 'Memo ' . $request->status . ' successfully.');
    }

    public function destroy(MeetingMemo $memo)
    {
        abort_if($memo->created_by !== auth()->id(), 403);
        abort_if($memo->status === 'approved', 422, 'Cannot delete an approved memo.');

        $memo->delete();

        return back()->with('success', 'Memo deleted.');
    }

    public function duplicate(MeetingMemo $memo)
    {
        abort_if($memo->created_by !== auth()->id(), 403);
        abort_if($memo->status !== 'rejected', 422, 'Only rejected memos can be duplicated.');

        $newMemo = $memo->replicate();
        $newMemo->status = 'draft';
        $newMemo->title = $newMemo->title . ' (Revision)';
        $newMemo->save();

        foreach ($memo->attachments as $attachment) {
            $newMemo->attachments()->create([
                'file_name' => $attachment->file_name,
                'file_path' => $attachment->file_path,
                'file_type' => $attachment->file_type,
                'file_size' => $attachment->file_size,
            ]);
        }

        return redirect()->route('memos.edit', $newMemo->id)
            ->with('success', 'Memo duplicated as a new draft. You can now revise it.');
    }

    public function downloadPdf(MeetingMemo $memo)
    {
        abort_if($memo->company_id !== auth()->user()->company_id, 403);
        if ($memo->status !== 'approved') {
            abort(403, 'Only approved memos can be downloaded.');
        }
        
        $memo->load(['creator', 'company', 'reviews.reviewer', 'checker', 'verifier', 'approver']);
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.memo', ['memo' => $memo]);
        return $pdf->download('memo-' . $memo->id . '.pdf');
    }
}
