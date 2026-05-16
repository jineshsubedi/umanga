<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MeetingMinuteAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'meeting_minute_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
    ];

    protected $appends = [
        'file_size_formatted',
    ];

    public function meetingMinute()
    {
        return $this->belongsTo(MeetingMinute::class);
    }

    public function getFileSizeFormattedAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }
}
