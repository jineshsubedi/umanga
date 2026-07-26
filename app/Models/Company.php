<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'email', 'phone', 'address', 'status',
        'has_checker', 'has_verifier', 'has_approver', 'departments', 'modules'
    ];

    protected $casts = [
        'has_checker' => 'boolean',
        'has_verifier' => 'boolean',
        'has_approver' => 'boolean',
        'departments' => 'array',
        'modules' => 'array',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function meetingMemos()
    {
        return $this->hasMany(MeetingMemo::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
