<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeetingMinuteReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'meeting_minute_id', 'reviewed_by', 'status', 'comment',
    ];

    public function meetingMinute()
    {
        return $this->belongsTo(MeetingMinute::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
