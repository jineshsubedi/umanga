<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcurementRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_number', 'date', 'company_id', 'department_id', 'requested_by',
        'item_name', 'specification', 'quantity', 'estimated_cost', 'priority',
        'purpose', 'attachment_path', 'vendor', 'status', 'workflow_id', 'current_step_id'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function workflow()
    {
        return $this->belongsTo(Workflow::class);
    }

    public function currentStep()
    {
        return $this->belongsTo(WorkflowStep::class, 'current_step_id');
    }

    public function reviews()
    {
        return $this->hasMany(ProcurementReview::class);
    }
}
