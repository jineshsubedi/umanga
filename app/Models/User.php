<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'company_id', 'name', 'email', 'password', 'role', 'designation', 'status', 'signature_path',
        'email_notifications', 'database_notifications',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'email_notifications' => 'boolean',
        'database_notifications' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function meetingMemos()
    {
        return $this->hasMany(MeetingMemo::class, 'created_by');
    }

    public function reviews()
    {
        return $this->hasMany(MeetingMemoReview::class, 'reviewed_by');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function isSuperAdmin(): bool { return $this->role === 'super_admin'; }
    public function isAdmin(): bool      { return $this->role === 'admin'; }
    public function isManager(): bool    { return $this->role === 'manager'; }
    public function isStaff(): bool     { return $this->role === 'staff'; }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new \App\Notifications\QueuedVerifyEmail);
    }
}
