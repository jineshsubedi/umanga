<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeetingMemo extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id', 'created_by', 'title', 'content', 'meeting_date', 'status',
        'checker_id', 'verifier_id', 'approver_id','workflow_id','current_step_id',
    ];

    protected $appends = ['formatted_meeting_date'];

    protected $casts = [
        'meeting_date' => 'datetime',
        'current_step_id' => 'integer',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reviews()
    {
        return $this->hasMany(MeetingMemoReview::class);
    }

    public function latestReview()
    {
        return $this->hasOne(MeetingMemoReview::class)->latestOfMany();
    }

    public function getFormattedMeetingDateAttribute()
    {
        return $this->meeting_date ? $this->meeting_date->format('jS M, Y g A') : null;
    }

    public function attachments()
    {
        return $this->hasMany(MeetingMemoAttachment::class);
    }

    public function managers()
    {
        return $this->belongsToMany(User::class, 'meeting_memo_managers', 'meeting_memo_id', 'manager_id');
    }

    public function checker()
    {
        return $this->belongsTo(User::class, 'checker_id');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verifier_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function workflow()
    {
        return $this->belongsTo(Workflow::class, 'workflow_id');
    }

    public function currentStep()
    {
        return $this->belongsTo(WorkflowStep::class, 'current_step_id');
    }
}
