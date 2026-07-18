<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Workflow;
use App\Models\WorkflowStep;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WorkflowController extends Controller
{
    public function index()
    {
        $workflows = Workflow::with('company')->latest()->get();
        return Inertia::render('SuperAdmin/Workflows/Index', [
            'workflows' => $workflows
        ]);
    }

    public function create()
    {
        $companies = Company::all();
        $roles = ['super_admin', 'admin', 'manager', 'staff'];
        $users = User::select('id', 'name', 'company_id')->get();

        return Inertia::render('SuperAdmin/Workflows/Create', [
            'companies' => $companies,
            'roles' => $roles,
            'users' => $users
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'module' => 'required|string|in:procurement,leave,memo',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'steps' => 'required|array|min:1',
            'steps.*.step_title' => 'nullable|string|max:100',
            'steps.*.approver_type' => 'required|string|in:role,specific_user,department_head',
            'steps.*.approver_value' => 'required_unless:steps.*.approver_type,department_head|nullable',
        ]);

        $workflow = Workflow::create([
            'company_id' => $validated['company_id'],
            'module' => $validated['module'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? '',
        ]);

        foreach ($validated['steps'] as $index => $stepData) {
            WorkflowStep::create([
                'workflow_id' => $workflow->id,
                'step_order' => $index + 1,
                'step_title' => $stepData['step_title'] ?? null,
                'approver_type' => $stepData['approver_type'],
                'approver_value' => $stepData['approver_value'] ?? null,
            ]);
        }

        return redirect()->route('super-admin.workflows.index')->with('success', 'Workflow created successfully.');
    }

    public function edit(Workflow $workflow)
    {
        $workflow->load('steps');
        $companies = Company::all();
        $roles = ['super_admin', 'admin', 'manager', 'staff'];
        $users = User::select('id', 'name', 'company_id')->get();

        return Inertia::render('SuperAdmin/Workflows/Edit', [
            'workflow' => $workflow,
            'companies' => $companies,
            'roles' => $roles,
            'users' => $users
        ]);
    }

    public function update(Request $request, Workflow $workflow)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'module' => 'required|string|in:procurement,leave,memo',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'steps' => 'required|array|min:1',
            'steps.*.step_title' => 'nullable|string|max:100',
            'steps.*.approver_type' => 'required|string|in:role,specific_user,department_head',
            'steps.*.approver_value' => 'required_unless:steps.*.approver_type,department_head|nullable',
        ]);

        $workflow->update([
            'company_id' => $validated['company_id'],
            'module' => $validated['module'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? '',
        ]);

        // Recreate steps
        $workflow->steps()->delete();
        foreach ($validated['steps'] as $index => $stepData) {
            WorkflowStep::create([
                'workflow_id' => $workflow->id,
                'step_order' => $index + 1,
                'step_title' => $stepData['step_title'] ?? null,
                'approver_type' => $stepData['approver_type'],
                'approver_value' => $stepData['approver_value'] ?? null,
            ]);
        }

        return redirect()->route('super-admin.workflows.index')->with('success', 'Workflow updated successfully.');
    }

    public function destroy(Workflow $workflow)
    {
        $workflow->delete();
        return redirect()->route('super-admin.workflows.index')->with('success', 'Workflow deleted successfully.');
    }
}
