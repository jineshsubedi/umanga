<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeetingMinute extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id', 'created_by', 'title', 'content', 'meeting_date', 'status',
    ];

    protected $casts = [
        'meeting_date' => 'date',
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
        return $this->hasMany(MeetingMinuteReview::class);
    }

    public function latestReview()
    {
        return $this->hasOne(MeetingMinuteReview::class)->latestOfMany();
    }
}
