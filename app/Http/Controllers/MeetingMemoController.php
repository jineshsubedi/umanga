<?php

namespace App\Http\Controllers;

use App\Models\MeetingMemo;
use App\Models\MeetingMemoReview;
use App\Models\User;
use App\Models\Workflow;
use App\Services\WorkflowService;
use App\Notifications\MemoReviewed;
use App\Notifications\MemoSubmitted;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MeetingMemoController extends Controller
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
            if (!$user->hasModuleAccess('memo')) {
                abort(403, 'You do not have access to the Memo module.');
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $status = $request->query('status', 'all');
        $user = auth()->user();

        if ($user->role === 'super_admin') {
            abort(403);
        }

        $query = MeetingMemo::where('company_id', $user->company_id)
            ->with(['creator:id,name', 'latestReview.reviewer:id,name', 'currentStep']);

        // Staff: only their own memos
        if ($user->role === 'staff') {
            $query->where('created_by', $user->id);
        }

        // Apply filters only for manager/admin role as per "while being a role manager/admin he/she can filter the lists"
        if ($user->role === 'manager' || $user->role === 'admin') {
            if ($request->search) {
                $query->where('title', 'like', "%{$request->search}%");
            }
            if ($request->creator) {
                $query->whereHas('creator', function ($q) use ($request) {
                    $q->where('name', 'like', "%{$request->creator}%");
                });
            }
            if ($request->date) {
                $query->whereDate('meeting_date', $request->date);
            }
        }

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $memos = $query->orderByDesc('id')->paginate(10)->withQueryString();

        // Counts query — scoped the same way as main query but without status filter
        $countQuery = MeetingMemo::where('company_id', $user->company_id);
        if ($user->role === 'staff') {
            $countQuery->where('created_by', $user->id);
        }
        if ($user->role === 'manager' || $user->role === 'admin') {
            if ($request->search) {
                $countQuery->where('title', 'like', "%{$request->search}%");
            }
            if ($request->creator) {
                $countQuery->whereHas('creator', function ($q) use ($request) {
                    $q->where('name', 'like', "%{$request->creator}%");
                });
            }
            if ($request->date) {
                $countQuery->whereDate('meeting_date', $request->date);
            }
        }

        $counts = [
            'all'      => (clone $countQuery)->count(),
            'pending'  => (clone $countQuery)->where('status', 'pending')->count(),
            'approved' => (clone $countQuery)->where('status', 'approved')->count(),
            'rejected' => (clone $countQuery)->where('status', 'rejected')->count(),
        ];

        return Inertia::render('MeetingMemos/Index', [
            'memos' => $memos,
            'counts' => $counts,
            'status' => $status,
            'filters' => $request->only(['search', 'creator', 'date'])
        ]);
    }

    public function create()
    {
        $company = auth()->user()->company;
        return Inertia::render('MeetingMemos/Create', compact('company'));
    }

    public function store(Request $request)
    {
        $company = auth()->user()->company;

        $request->validate([
            'title'        => 'required|string|max:255',
            'content'      => 'required|string',
            'meeting_date' => 'required|date',
            'attachments.*'=> 'nullable|file|max:10240',
        ]);

        $workflow = Workflow::where('company_id', $company->id)
                            ->where('module', 'memo')
                            ->whereHas('steps')
                            ->latest()
                            ->first();

        if (!$workflow) {
            return back()->withErrors([
                'workflow' => 'No approval workflow is configured for this company. Please ask your Super Admin to create one under Workflows → Create Workflow (Memo module).'
            ]);
        }

        $firstStep = $workflow->steps()->orderBy('step_order')->first();

        if (!$firstStep) {
            return back()->withErrors(['workflow' => 'The configured workflow has no steps. Please ask your Super Admin to add approval steps.']);
        }

        $meetingMemo = MeetingMemo::create([
            'company_id'      => $company->id,
            'created_by'      => auth()->id(),
            'title'           => $request->title,
            'content'         => $request->content,
            'meeting_date'    => $request->meeting_date,
            'status'          => 'pending',
            'workflow_id'     => $workflow->id,
            'current_step_id' => $firstStep->id,
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

        // Notify the first step's approvers
        $approvers = $this->workflowService->getApproversForStep($firstStep, $meetingMemo);
        foreach ($approvers as $approver) {
            $approver->notify(new \App\Notifications\MemoSubmitted($meetingMemo));
        }

        return redirect()->route('memos.index')->with('success', 'Memo created and successfully submitted to workflow. Awaiting approval from ' . ($firstStep->step_title ?: 'Step 1') . '.');
    }

    public function show(MeetingMemo $memo)
    {
        $user = auth()->user();
        abort_if($memo->company_id !== $user->company_id, 403);

        // Auto-heal pending memos whose workflow state is broken:
        // Case 1: No workflow assigned (submitted before workflow was configured)
        // Case 2: current_step_id points to a deleted step (workflow was edited/re-saved)
        if ($memo->status === 'pending') {
            $needsHeal = !$memo->workflow_id;

            if (!$needsHeal && $memo->current_step_id) {
                // Check if current_step_id still exists in workflow_steps
                $stepExists = \App\Models\WorkflowStep::where('id', $memo->current_step_id)
                                ->where('workflow_id', $memo->workflow_id)
                                ->exists();
                $needsHeal = !$stepExists;
            }

            if ($needsHeal) {
                // Find the latest valid workflow with steps for this company module
                $workflow = Workflow::where('company_id', $memo->company_id)
                                    ->where('module', 'memo')
                                    ->latest()
                                    ->first();

                if ($workflow && $workflow->steps()->count() > 0) {
                    // Count how many steps have already been approved for this memo
                    $approvedCount = $memo->reviews()->where('status', 'approved')->count();

                    // Find the next step after the already-approved ones
                    $nextStep = $workflow->steps()
                                        ->orderBy('step_order')
                                        ->skip($approvedCount)
                                        ->first();

                    $memo->update([
                        'workflow_id'     => $workflow->id,
                        'current_step_id' => $nextStep?->id,
                    ]);
                    $memo->refresh();
                }
            }
        }

        $memo->load(['creator:id,name', 'reviews.reviewer:id,name,signature_path', 'reviews.workflowStep', 'attachments', 'workflow.steps']);

        $canApprove = false;
        if ($memo->status === 'pending') {
            $canApprove = $this->workflowService->canUserApprove($user, $memo);
        }

        return Inertia::render('MeetingMemos/Show', [
            'memo'       => $memo,
            'canApprove' => $canApprove
        ]);
    }

    public function edit(MeetingMemo $memo)
    {
        abort_if($memo->created_by !== auth()->id(), 403);
        abort_if($memo->status !== 'draft', 422, 'Cannot edit this memo.');
        
        $memo->load(['attachments']);
        $company = auth()->user()->company;

        return Inertia::render('MeetingMemos/Edit', [
            'memo' => $memo,
            'company' => $company,
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
            'attachments.*'=> 'nullable|file|max:10240',
        ]);

        $memo->update([
            'title' => $request->title,
            'content' => $request->content,
            'meeting_date' => $request->meeting_date,
        ]);

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

        // Find the latest workflow for this company+module that actually has steps configured.
        // This avoids picking empty/test workflows that were created earlier.
        $workflow = Workflow::where('company_id', $memo->company_id)
                            ->where('module', 'memo')
                            ->whereHas('steps')   // Only workflows WITH steps
                            ->latest()
                            ->first();

        if (!$workflow) {
            // No valid workflow with steps exists — give a clear error instead of auto-approving.
            return back()->withErrors([
                'workflow' => 'No approval workflow is configured for this company. Please ask your Super Admin to create one under Workflows → Create Workflow (Memo module).'
            ]);
        }

        // Get the first step of the workflow
        $firstStep = $workflow->steps()->orderBy('step_order')->first();

        if (!$firstStep) {
            return back()->withErrors(['workflow' => 'The configured workflow has no steps. Please ask your Super Admin to add approval steps.']);
        }

        $memo->update([
            'status'          => 'pending',
            'workflow_id'     => $workflow->id,
            'current_step_id' => $firstStep->id,
        ]);

        // Notify the first step's approvers
        $approvers = $this->workflowService->getApproversForStep($firstStep, $memo);
        foreach ($approvers as $approver) {
            $approver->notify(new MemoSubmitted($memo));
        }

        return back()->with('success', 'Memo submitted successfully. Awaiting approval from ' . ($firstStep->step_title ?: 'Step 1') . '.');
    }

    public function review(Request $request, MeetingMemo $memo)
    {
        $user = auth()->user();
        abort_if($memo->company_id !== $user->company_id, 403);
        
        if ($memo->status !== 'pending') {
            return back()->withErrors(['status' => 'This memo is no longer pending.']);
        }

        if (!$this->workflowService->canUserApprove($user, $memo)) {
            return back()->withErrors(['unauthorized' => 'You are not authorized to approve this memo at this stage.']);
        }

        $request->validate([
            'status'  => 'required|in:approved,rejected,returned',
            'comment' => 'nullable|string|max:1000',
            'signature_data' => 'nullable|string'
        ]);

        $signatureData = $request->signature_data;
        if (!$signatureData && $user->signature_path) {
            $signatureData = \Illuminate\Support\Facades\Storage::disk('public')->url($user->signature_path);
        }

        MeetingMemoReview::create([
            'meeting_memo_id' => $memo->id,
            'reviewed_by'     => $user->id,
            'workflow_step_id'=> $memo->current_step_id,
            'status'          => $request->status, // Map approved -> Approve for display later
            'comment'         => $request->comment,
            'signature_data'  => $signatureData
        ]);

        if ($request->status === 'approved') {
            $nextStep = $this->workflowService->getNextStep($memo->workflow_id, $memo->current_step_id);
            
            if ($nextStep) {
                $memo->update(['current_step_id' => $nextStep->id]);
                
                $nextApprovers = $this->workflowService->getApproversForStep($nextStep, $memo);
                foreach ($nextApprovers as $approver) {
                    $approver->notify(new MemoSubmitted($memo));
                }
            } else {
                $memo->update([
                    'status' => 'approved',
                    'current_step_id' => null
                ]);
            }
        } elseif ($request->status === 'rejected') {
            $memo->update([
                'status' => 'rejected',
                'current_step_id' => null
            ]);
        } elseif ($request->status === 'returned') {
            $memo->update([
                'status' => 'returned',
                'current_step_id' => null
            ]);
        }

        // Notify creator
        $memo->creator->notify(new MemoReviewed(
            $memo,
            $request->status,
            $request->comment,
            $user->name
        ));

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
        abort_if($memo->status !== 'rejected' && $memo->status !== 'returned', 422, 'Only rejected or returned memos can be duplicated.');

        $newMemo = $memo->replicate();
        $newMemo->status = 'draft';
        $newMemo->workflow_id = null;
        $newMemo->current_step_id = null;
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
        
        $memo->load(['creator', 'company', 'reviews.reviewer', 'reviews.workflowStep']);
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.memo', ['memo' => $memo]);
        return $pdf->download('memo-' . $memo->id . '.pdf');
    }
}
