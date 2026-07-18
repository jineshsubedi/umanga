<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkflowStep extends Model
{
    use HasFactory;

    protected $fillable = ['workflow_id', 'step_order', 'step_title', 'approver_type', 'approver_value'];

    public function workflow()
    {
        return $this->belongsTo(Workflow::class);
    }
}
