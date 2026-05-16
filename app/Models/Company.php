<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'phone', 'address', 'status'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function meetingMinutes()
    {
        return $this->hasMany(MeetingMinute::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
