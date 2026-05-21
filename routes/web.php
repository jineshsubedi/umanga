<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SuperAdmin\CompanyController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Manager\MeetingMinuteController as ManagerMeetingMinuteController;
use App\Http\Controllers\Client\MeetingMinuteController as ClientMeetingMinuteController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin'      => Route::has('login'),
        'canRegister'   => Route::has('register'),
        'laravelVersion'=> Application::VERSION,
        'phpVersion'    => PHP_VERSION,
    ]);
});

// Dashboard — redirects based on role
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Notifications
Route::middleware('auth')->group(function () {
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
    Route::get('/notifications/{id}', [\App\Http\Controllers\NotificationController::class, 'markRead'])->name('notifications.markRead');
});

// ─── Attendance (all authenticated non-superadmin users) ────────────────────
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/attendance/clock-in',  [\App\Http\Controllers\AttendanceController::class, 'clockIn'])->name('attendance.clock-in');
    Route::post('/attendance/clock-out', [\App\Http\Controllers\AttendanceController::class, 'clockOut'])->name('attendance.clock-out');
});

// ─── Super Admin ─────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:super_admin'])
    ->prefix('super-admin')
    ->name('super-admin.')
    ->group(function () {
        Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/companies', [CompanyController::class, 'index'])->name('companies.index');
        Route::get('/companies/{company}', [CompanyController::class, 'show'])->name('companies.show');
        Route::patch('/companies/{company}/toggle-status', [CompanyController::class, 'toggleStatus'])->name('companies.toggle-status');
        Route::get('/attendance', [\App\Http\Controllers\SuperAdmin\AttendanceController::class, 'index'])->name('attendance.index');
        Route::get('/users', [\App\Http\Controllers\SuperAdmin\UserController::class, 'index'])->name('users.index');
        Route::patch('/users/{user}/toggle-status', [\App\Http\Controllers\SuperAdmin\UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::get('/settings', [\App\Http\Controllers\SuperAdmin\SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [\App\Http\Controllers\SuperAdmin\SettingController::class, 'update'])->name('settings.update');
    });

// ─── Admin ───────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::resource('users', UserController::class)->except(['show']);
        Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::get('/meeting-minutes', [\App\Http\Controllers\Admin\MeetingMinuteController::class, 'index'])->name('meeting-minutes.index');
        Route::get('/meeting-minutes/{meetingMinute}', [\App\Http\Controllers\Admin\MeetingMinuteController::class, 'show'])->name('meeting-minutes.show');
        Route::get('/attendance', [\App\Http\Controllers\Admin\AttendanceController::class, 'index'])->name('attendance.index');
        Route::get('/calendar', [\App\Http\Controllers\Admin\CalendarController::class, 'index'])->name('calendar.index');
    });

// ─── Manager ─────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'role:manager'])
    ->prefix('manager')
    ->name('manager.')
    ->group(function () {
        Route::get('/meeting-minutes', [ManagerMeetingMinuteController::class, 'index'])->name('meeting-minutes.index');
        Route::get('/meeting-minutes/{meetingMinute}', [ManagerMeetingMinuteController::class, 'show'])->name('meeting-minutes.show');
        Route::post('/meeting-minutes/{meetingMinute}/review', [ManagerMeetingMinuteController::class, 'review'])->name('meeting-minutes.review');
        Route::get('/attendance', [\App\Http\Controllers\Manager\AttendanceController::class, 'index'])->name('attendance.index');
    });

// ─── Client ──────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'role:client'])
    ->prefix('client')
    ->name('client.')
    ->group(function () {
        Route::get('/meeting-minutes', [ClientMeetingMinuteController::class, 'index'])->name('meeting-minutes.index');
        Route::get('/meeting-minutes/create', [ClientMeetingMinuteController::class, 'create'])->name('meeting-minutes.create');
        Route::post('/meeting-minutes', [ClientMeetingMinuteController::class, 'store'])->name('meeting-minutes.store');
        Route::get('/meeting-minutes/{meetingMinute}', [ClientMeetingMinuteController::class, 'show'])->name('meeting-minutes.show');
        Route::get('/meeting-minutes/{meetingMinute}/edit', [ClientMeetingMinuteController::class, 'edit'])->name('meeting-minutes.edit');
        Route::match(['put', 'post'], '/meeting-minutes/{meetingMinute}', [ClientMeetingMinuteController::class, 'update'])->name('meeting-minutes.update');
        Route::delete('/meeting-minutes/{meetingMinute}', [ClientMeetingMinuteController::class, 'destroy'])->name('meeting-minutes.destroy');
        Route::post('/meeting-minutes/{meetingMinute}/submit', [ClientMeetingMinuteController::class, 'submit'])->name('meeting-minutes.submit');
        Route::post('/meeting-minutes/{meetingMinute}/duplicate', [ClientMeetingMinuteController::class, 'duplicate'])->name('meeting-minutes.duplicate');
        Route::delete('/meeting-minutes/attachments/{attachment}', [\App\Http\Controllers\Client\AttachmentController::class, 'destroy'])->name('meeting-minutes.attachments.destroy');
        Route::get('/attendance', [\App\Http\Controllers\Client\AttendanceController::class, 'index'])->name('attendance.index');
    });

require __DIR__.'/auth.php';
