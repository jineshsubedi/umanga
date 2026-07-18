<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcurementReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'procurement_request_id', 'reviewer_id', 'workflow_step_id', 'action', 'comment', 'signature_data'
    ];

    public function procurementRequest()
    {
        return $this->belongsTo(ProcurementRequest::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function workflowStep()
    {
        return $this->belongsTo(WorkflowStep::class);
    }
}
