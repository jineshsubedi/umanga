<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\MeetingMemo;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MeetingMemoController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'all');
        $search = $request->query('search', '');
        $company_id = $request->query('company_id', '');
        $staff_id = $request->query('staff_id', '');
        $approver_id = $request->query('approver_id', '');

        $query = MeetingMemo::where('status', '!=', 'draft')
            ->with(['creator:id,name', 'company:id,name', 'latestReview.reviewer:id,name', 'checker:id,name', 'verifier:id,name', 'approver:id,name']);

        if ($status !== 'all') {
            if ($status === 'pending') {
                $query->whereIn('status', ['pending_checker', 'pending_verifier', 'pending_approver']);
            } else {
                $query->where('status', $status);
            }
        }

        if ($search) {
            $query->where('title', 'like', '%' . $search . '%');
        }

        if ($company_id) {
            $query->where('company_id', $company_id);
        }

        if ($staff_id) {
            $query->where('created_by', $staff_id);
        }

        if ($approver_id) {
            $query->whereHas('reviews', function ($q) use ($approver_id) {
                $q->where('reviewer_id', $approver_id)->where('status', 'approved');
            });
        }

        $memos = $query->latest()->get();

        $countsQuery = MeetingMemo::where('status', '!=', 'draft');
        
        // Apply the same filters to the counts
        if ($search) $countsQuery->where('title', 'like', '%' . $search . '%');
        if ($company_id) $countsQuery->where('company_id', $company_id);
        if ($staff_id) $countsQuery->where('created_by', $staff_id);
        if ($approver_id) {
            $countsQuery->whereHas('reviews', function ($q) use ($approver_id) {
                $q->where('reviewer_id', $approver_id)->where('status', 'approved');
            });
        }

        $counts = [
            'all'      => (clone $countsQuery)->count(),
            'pending'  => (clone $countsQuery)->whereIn('status', ['pending_checker', 'pending_verifier', 'pending_approver'])->count(),
            'approved' => (clone $countsQuery)->where('status', 'approved')->count(),
            'rejected' => (clone $countsQuery)->where('status', 'rejected')->count(),
        ];

        $companies = Company::select('id', 'name')->get();
        // For staff, those who can create memos (staff, maybe others)
        $staffs = User::select('id', 'name')->where('role', 'staff')->get();
        // For approvers
        $approvers = User::whereIn('role', ['manager', 'admin'])->select('id', 'name')->get();

        $filters = [
            'search' => $search,
            'company_id' => $company_id,
            'staff_id' => $staff_id,
            'approver_id' => $approver_id,
        ];

        return Inertia::render('SuperAdmin/MeetingMemos/Index', compact('memos', 'counts', 'status', 'companies', 'staffs', 'approvers', 'filters'));
    }

    public function show(MeetingMemo $meetingMemo)
    {
        $meetingMemo->load(['creator:id,name', 'company:id,name', 'reviews.reviewer:id,name', 'attachments', 'checker:id,name', 'verifier:id,name', 'approver:id,name']);

        return Inertia::render('SuperAdmin/MeetingMemos/Show', ['memo' => $meetingMemo]);
    }

    public function downloadPdf(MeetingMemo $meetingMemo)
    {
        if ($meetingMemo->status !== 'approved') {
            abort(403, 'Only approved memos can be downloaded.');
        }

        $meetingMemo->load(['creator', 'company', 'reviews.reviewer']);
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.memo', ['memo' => $meetingMemo]);
        return $pdf->download('memo-' . $meetingMemo->id . '.pdf');
    }
}
