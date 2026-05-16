<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    protected $fillable = [
        'user_id', 'company_id', 'date', 'clock_in', 'clock_out',
        'clock_in_lat', 'clock_in_lng', 'clock_in_address',
        'clock_out_lat', 'clock_out_lng', 'clock_out_address',
    ];

    protected $casts = [
        'date'      => 'date',
        'clock_in'  => 'datetime',
        'clock_out' => 'datetime',
    ];

    protected $appends = ['total_hours', 'formatted_clock_in', 'formatted_clock_out'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function getTotalHoursAttribute(): ?string
    {
        if (!$this->clock_in || !$this->clock_out) {
            return null;
        }
        $diff = $this->clock_in->diff($this->clock_out);
        return sprintf('%dh %02dm', $diff->h + ($diff->days * 24), $diff->i);
    }

    public function getFormattedClockInAttribute(): ?string
    {
        return $this->clock_in ? $this->clock_in->format('g:i A') : null;
    }

    public function getFormattedClockOutAttribute(): ?string
    {
        return $this->clock_out ? $this->clock_out->format('g:i A') : null;
    }
}
