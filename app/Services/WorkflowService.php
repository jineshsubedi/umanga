<?php

namespace App\Services;

use App\Models\Department;
use App\Models\User;
use App\Models\WorkflowStep;
use Illuminate\Database\Eloquent\Model;

class WorkflowService
{
    /**
     * Get the next workflow step for a given workflow and current step.
     */
    public function getNextStep($workflowId, $currentStepId = null)
    {
        $query = WorkflowStep::where('workflow_id', $workflowId)->orderBy('step_order');
        
        if ($currentStepId) {
            $currentStep = WorkflowStep::find($currentStepId);
            if ($currentStep) {
                $query->where('step_order', '>', $currentStep->step_order);
            }
        }
        
        return $query->first();
    }

    /**
     * Resolve the eligible approvers for a given workflow step and trackable model.
     * The trackable model (e.g., ProcurementRequest, MeetingMemo) provides context like company_id and department_id.
     */
    public function getApproversForStep(WorkflowStep $step, Model $trackable)
    {
        $type = $step->approver_type;
        $value = $step->approver_value;
        $companyId = $trackable->company_id;

        if ($type === 'specific_user') {
            return User::where('id', $value)->get();
        } 
        
        if ($type === 'role') {
            return User::where('company_id', $companyId)
                ->where('role', $value)
                ->where('status', 'active')
                ->get();
        } 
        
        if ($type === 'department_head') {
            $departmentId = null;
            
            // Check if trackable has a department_id (like ProcurementRequest)
            if (isset($trackable->department_id)) {
                $departmentId = $trackable->department_id;
            } 
            // Fallback to the requester's or creator's department
            elseif (isset($trackable->requested_by) || isset($trackable->created_by)) {
                $userId = $trackable->requested_by ?? $trackable->created_by;
                $user = User::find($userId);
                $departmentId = $user ? $user->department_id : null;
            }

            if ($departmentId) {
                $department = Department::find($departmentId);
                if ($department && $department->head_of_department_id) {
                    return User::where('id', $department->head_of_department_id)
                        ->where('status', 'active')
                        ->get();
                }
            }
        }

        return collect();
    }

    /**
     * Determine if a user can approve the current step of a trackable item.
     */
    public function canUserApprove(User $user, Model $trackable)
    {
        if (!$trackable->current_step_id) {
            return false;
        }

        $currentStep = WorkflowStep::find($trackable->current_step_id);
        if (!$currentStep) {
            return false;
        }

        $approvers = $this->getApproversForStep($currentStep, $trackable);
        return $approvers->contains('id', $user->id);
    }
}
