<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ProcurementRequest;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProcurementRequestController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'all');
        $search = $request->query('search', '');
        $company_id = $request->query('company_id', '');
        $staff_id = $request->query('staff_id', '');
        $approver_id = $request->query('approver_id', '');

        $query = ProcurementRequest::where('status', '!=', 'draft')
            ->with(['requester:id,name', 'company:id,name', 'currentStep']);

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
            $query->where('requested_by', $staff_id);
        }

        if ($approver_id) {
            $query->whereHas('reviews', function ($q) use ($approver_id) {
                $q->where('reviewer_id', $approver_id)->where('status', 'approved');
            });
        }

        $procurements = $query->orderByDesc('id')->paginate(10)->withQueryString();

        $countsQuery = ProcurementRequest::where('status', '!=', 'draft');
        
        // Apply the same filters to the counts
        if ($search) $countsQuery->where('title', 'like', '%' . $search . '%');
        if ($company_id) $countsQuery->where('company_id', $company_id);
        if ($staff_id) $countsQuery->where('requested_by', $staff_id);
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
        // For staff, those who have submitted the procurement
        $staffIds = ProcurementRequest::where('status', '!=', 'draft')->distinct()->pluck('requested_by');
        $staffs = User::whereIn('id', $staffIds)->select('id', 'name')->get();

        // For approvers, those assigned on the procurement workflows
        $workflowSteps = \App\Models\WorkflowStep::whereHas('workflow', function ($q) {
            $q->where('module', 'procurement');
        })->get();

        $approverUserIds = $workflowSteps->where('approver_type', 'user')->pluck('approver_value')->toArray();
        $approverRoles = $workflowSteps->where('approver_type', 'role')->pluck('approver_value')->toArray();

        if (empty($approverUserIds) && empty($approverRoles)) {
            $approvers = collect();
        } else {
            $approversQuery = User::select('id', 'name')->where(function($q) use ($approverUserIds, $approverRoles) {
                if (!empty($approverUserIds)) {
                    $q->orWhereIn('id', $approverUserIds);
                }
                if (!empty($approverRoles)) {
                    $q->orWhereIn('role', $approverRoles);
                }
            });
            $approvers = $approversQuery->get();
        }

        $filters = [
            'search' => $search,
            'company_id' => $company_id,
            'staff_id' => $staff_id,
            'approver_id' => $approver_id,
        ];

        return Inertia::render('SuperAdmin/ProcurementRequests/Index', compact('procurements', 'counts', 'status', 'companies', 'staffs', 'approvers', 'filters'));
    }

    public function show(ProcurementRequest $procurementRequest)
    {
        $procurementRequest->load(['requester:id,name', 'company:id,name', 'department:id,name', 'reviews.reviewer:id,name', 'attachments', 'workflow.steps', 'reviews.workflowStep']);

        return Inertia::render('SuperAdmin/ProcurementRequests/Show', ['procurement' => $procurementRequest]);
    }

    public function downloadPdf(ProcurementRequest $procurementRequest)
    {
        if ($procurementRequest->status !== 'approved') {
            abort(403, 'Only approved procurements can be downloaded.');
        }

        $procurementRequest->load(['requester', 'company', 'reviews.reviewer']);
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.procurement', ['procurement' => $procurementRequest]);
        return $pdf->download('procurement-' . $procurementRequest->id . '.pdf');
    }
}
