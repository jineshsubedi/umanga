<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use NotificationChannels\WebPush\HasPushSubscriptions;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasPushSubscriptions;

    protected $fillable = [
        'company_id', 'name', 'email', 'password', 'role', 'designation', 'status', 'signature_path',
        'email_notifications', 'database_notifications', 'push_notifications', 'password_changed_at',
        'is_checker', 'is_verifier', 'is_approver', 'department',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'email_notifications' => 'boolean',
        'database_notifications' => 'boolean',
        'push_notifications' => 'boolean',
        'password_changed_at' => 'datetime',
        'is_checker' => 'boolean',
        'is_verifier' => 'boolean',
        'is_approver' => 'boolean',
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
        // $this->notify(new \App\Notifications\QueuedVerifyEmail);
        $this->notify(new \Illuminate\Auth\Notifications\VerifyEmail);
    }
}
