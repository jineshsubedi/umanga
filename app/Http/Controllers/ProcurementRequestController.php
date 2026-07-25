<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\ProcurementRequest;
use App\Models\ProcurementReview;
use App\Models\Workflow;
use App\Services\WorkflowService;
use App\Notifications\ProcurementSubmitted;
use App\Notifications\ProcurementReviewed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Illuminate\Support\Str;

class ProcurementRequestController extends Controller
{
    protected $workflowService;

    public function __construct(WorkflowService $workflowService)
    {
        $this->workflowService = $workflowService;
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            if ($user->isSuperAdmin()) {
                abort(403);
            }
            if (!$user->hasModuleAccess('procurement')) {
                abort(403, 'You do not have access to the Procurement module.');
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        
        $baseQuery = ProcurementRequest::query();

        // Staff sees their own. Managers/Admins might see company wide or departmental.
        if ($user->role === 'staff') {
            $baseQuery->where('requested_by', $user->id);
        } elseif ($user->role === 'manager' || $user->role === 'admin') {
            $baseQuery->where('company_id', $user->company_id);
        }

        // Apply filters only for manager/admin role as per "while being a role manager/admin he/she can filter the lists"
        if ($user->role === 'manager' || $user->role === 'admin') {
            if ($request->search) {
                $baseQuery->where(function($q) use ($request) {
                    $q->where('request_number', 'like', "%{$request->search}%")
                      ->orWhere('item_name', 'like', "%{$request->search}%");
                });
            }
            if ($request->creator) {
                $baseQuery->whereHas('requester', function ($q) use ($request) {
                    $q->where('name', 'like', "%{$request->creator}%");
                });
            }
            if ($request->date) {
                $baseQuery->whereDate('date', $request->date);
            }
        }

        $counts = [
            'all' => (clone $baseQuery)->count(),
            'pending' => (clone $baseQuery)->where('status', 'Pending')->count(),
            'approved' => (clone $baseQuery)->where('status', 'Approved')->count(),
            'rejected' => (clone $baseQuery)->whereIn('status', ['Rejected', 'Returned'])->count(),
        ];

        $query = $baseQuery->with(['company', 'department', 'requester', 'currentStep']);

        if ($request->status) {
            if ($request->status === 'all') {
                // do nothing
            } elseif ($request->status === 'pending') {
                $query->where('status', 'Pending');
            } elseif ($request->status === 'approved') {
                $query->where('status', 'Approved');
            } elseif ($request->status === 'rejected') {
                $query->whereIn('status', ['Rejected', 'Returned']);
            }
        }

        $requests = $query->orderByDesc('id')->paginate(15)->withQueryString();

        return Inertia::render('Procurement/Index', [
            'requests' => $requests,
            'counts' => $counts,
            'status' => $request->status ?? 'all',
            'filters' => $request->only(['search', 'creator', 'date', 'status'])
        ]);
    }

    public function create()
    {
        $user = auth()->user();
        $companies = $user->isSuperAdmin() ? Company::all() : Company::where('id', $user->company_id)->get();
        
        // Departments of the selected company would be fetched asynchronously or passed. 
        // For simplicity, we can just load departments for the user's company.
        $departments = \App\Models\Department::whereIn('company_id', $companies->pluck('id'))->get();

        return Inertia::render('Procurement/Create', [
            'companies' => $companies,
            'departments' => $departments
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'department_id' => 'nullable|exists:departments,id',
            'item_name' => 'required|string|max:255',
            'specification' => 'nullable|string',
            'quantity' => 'required|integer|min:1',
            'estimated_cost' => 'nullable|numeric|min:0',
            'priority' => 'required|string|in:High,Medium,Normal,Low',
            'purpose' => 'nullable|string',
            'vendor' => 'nullable|string',
            'attachment' => 'nullable|file|max:10240', // 10MB max
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('procurement_attachments', 'public');
        }

        // Find the active workflow for this company and module
        $workflow = Workflow::where('company_id', $validated['company_id'])
                            ->where('module', 'procurement')
                            ->first();

        if (!$workflow) {
            return back()->withErrors(['workflow' => 'No active procurement workflow found for this company. Please contact administrator.']);
        }

        $firstStep = $this->workflowService->getNextStep($workflow->id);

        $procurementRequest = ProcurementRequest::create([
            'request_number' => 'PR-' . strtoupper(Str::random(8)),
            'date' => now(),
            'company_id' => $validated['company_id'],
            'department_id' => $validated['department_id'],
            'requested_by' => $user->id,
            'item_name' => $validated['item_name'],
            'specification' => $validated['specification'],
            'quantity' => $validated['quantity'],
            'estimated_cost' => $validated['estimated_cost'],
            'priority' => $validated['priority'],
            'purpose' => $validated['purpose'],
            'vendor' => $validated['vendor'],
            'attachment_path' => $attachmentPath,
            'status' => 'Pending',
            'workflow_id' => $workflow->id,
            'current_step_id' => $firstStep ? $firstStep->id : null,
        ]);

        // If no steps, auto-complete
        if (!$firstStep) {
            $procurementRequest->update(['status' => 'Completed']);
        } else {
            // Notify first step approvers
            $approvers = $this->workflowService->getApproversForStep($firstStep, $procurementRequest);
            foreach ($approvers as $approver) {
                $approver->notify(new ProcurementSubmitted($procurementRequest));
            }
        }

        return redirect()->route('procurement.index')->with('success', 'Procurement request submitted successfully.');
    }

    public function show(ProcurementRequest $procurement)
    {
        $procurement->load(['company', 'department', 'requester', 'reviews.reviewer', 'reviews.workflowStep', 'workflow.steps']);
        
        $canApprove = false;
        if ($procurement->status === 'Pending') {
            $canApprove = $this->workflowService->canUserApprove(auth()->user(), $procurement);
        }

        return Inertia::render('Procurement/Show', [
            'procurement' => $procurement,
            'canApprove' => $canApprove,
        ]);
    }

    public function action(Request $request, ProcurementRequest $procurement)
    {
        $user = auth()->user();

        if ($procurement->status !== 'Pending') {
            return back()->withErrors(['status' => 'This request is no longer pending.']);
        }

        if (!$this->workflowService->canUserApprove($user, $procurement)) {
            return back()->withErrors(['unauthorized' => 'You are not authorized to approve this request at this stage.']);
        }

        $validated = $request->validate([
            'action' => 'required|in:Approve,Reject,Return',
            'comment' => 'nullable|string',
            'signature_data' => 'nullable|string' // base64 image data
        ]);

        // Signature logic: use provided drawn signature, or fallback to user's saved signature profile
        $signatureData = $validated['signature_data'];
        if (!$signatureData && $user->signature_path) {
            $signatureData = Storage::disk('public')->url($user->signature_path);
        }

        // Record the review
        ProcurementReview::create([
            'procurement_request_id' => $procurement->id,
            'reviewer_id' => $user->id,
            'workflow_step_id' => $procurement->current_step_id,
            'action' => $validated['action'],
            'comment' => $validated['comment'],
            'signature_data' => $signatureData,
        ]);

        if ($validated['action'] === 'Approve') {
            $nextStep = $this->workflowService->getNextStep($procurement->workflow_id, $procurement->current_step_id);
            
            if ($nextStep) {
                $procurement->update(['current_step_id' => $nextStep->id]);
                // TODO: Trigger Notification to next approvers
            } else {
                $procurement->update([
                    'status' => 'Approved', // or Completed
                    'current_step_id' => null
                ]);
            }
        } elseif ($validated['action'] === 'Reject') {
            $procurement->update([
                'status' => 'Rejected',
                'current_step_id' => null
            ]);
        } elseif ($validated['action'] === 'Return') {
            $procurement->update([
                'status' => 'Returned',
                // For 'Return', it might go back to the requester, so we clear the current step.
                // The requester will have to re-submit.
                'current_step_id' => null 
            ]);
        }

        // Notify the requester about the review action
        $procurement->requester->notify(new ProcurementReviewed(
            $procurement, 
            $validated['action'], 
            $validated['comment'], 
            $user->name
        ));

        // If approved and there is a next step, notify the next approvers
        if ($validated['action'] === 'Approve' && isset($nextStep) && $nextStep) {
            $nextApprovers = $this->workflowService->getApproversForStep($nextStep, $procurement);
            foreach ($nextApprovers as $approver) {
                $approver->notify(new ProcurementSubmitted($procurement));
            }
        }

        return redirect()->route('procurement.show', $procurement->id)->with('success', "Request {$validated['action']}d successfully.");
    }
}
