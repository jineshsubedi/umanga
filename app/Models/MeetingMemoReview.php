<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeetingMemoReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'meeting_memo_id', 'reviewed_by', 'workflow_step_id', 'status', 'comment', 'signature_data',
    ];

    public function meetingMemo()
    {
        return $this->belongsTo(MeetingMemo::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function workflowStep()
    {
        return $this->belongsTo(WorkflowStep::class, 'workflow_step_id');
    }
}
