<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SuperAdmin\CompanyController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Manager\MeetingMemoController as ManagerMeetingMemoController;
use App\Http\Controllers\SuperAdmin\MeetingMemoController as SuperAdminMeetingMemoController;
use App\Http\Controllers\Staff\MeetingMemoController as StaffMeetingMemoController;
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
        Route::get('/companies/create', [CompanyController::class, 'create'])->name('companies.create');
        Route::post('/companies', [CompanyController::class, 'store'])->name('companies.store');
        Route::get('/companies/{company}', [CompanyController::class, 'show'])->name('companies.show');
        Route::get('/companies/{company}/edit', [CompanyController::class, 'edit'])->name('companies.edit');
        Route::match(['put', 'patch'], '/companies/{company}', [CompanyController::class, 'update'])->name('companies.update');
        Route::delete('/companies/{company}', [CompanyController::class, 'destroy'])->name('companies.destroy');
        Route::patch('/companies/{company}/toggle-status', [CompanyController::class, 'toggleStatus'])->name('companies.toggle-status');
        Route::get('/attendance', [\App\Http\Controllers\SuperAdmin\AttendanceController::class, 'index'])->name('attendance.index');
        Route::get('/users', [\App\Http\Controllers\SuperAdmin\UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [\App\Http\Controllers\SuperAdmin\UserController::class, 'create'])->name('users.create');
        Route::post('/users', [\App\Http\Controllers\SuperAdmin\UserController::class, 'store'])->name('users.store');
        Route::get('/users/bulk-create', [\App\Http\Controllers\SuperAdmin\UserController::class, 'bulkCreate'])->name('users.bulk-create');
        Route::post('/users/bulk', [\App\Http\Controllers\SuperAdmin\UserController::class, 'bulkStore'])->name('users.bulk-store');
        Route::get('/users/{user}/edit', [\App\Http\Controllers\SuperAdmin\UserController::class, 'edit'])->name('users.edit');
        Route::match(['put', 'patch'], '/users/{user}', [\App\Http\Controllers\SuperAdmin\UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [\App\Http\Controllers\SuperAdmin\UserController::class, 'destroy'])->name('users.destroy');
        Route::patch('/users/{user}/toggle-status', [\App\Http\Controllers\SuperAdmin\UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::get('/meeting-memos', [SuperAdminMeetingMemoController::class, 'index'])->name('meeting-memos.index');
        Route::get('/meeting-memos/{meetingMemo}', [SuperAdminMeetingMemoController::class, 'show'])->name('meeting-memos.show');
        Route::get('/meeting-memos/{meetingMemo}/pdf', [SuperAdminMeetingMemoController::class, 'downloadPdf'])->name('meeting-memos.pdf');
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
        Route::get('/meeting-memos', [\App\Http\Controllers\Admin\MeetingMemoController::class, 'index'])->name('meeting-memos.index');
        Route::get('/meeting-memos/{meetingMemo}', [\App\Http\Controllers\Admin\MeetingMemoController::class, 'show'])->name('meeting-memos.show');
        Route::post('/meeting-memos/{meetingMemo}/review', [\App\Http\Controllers\Admin\MeetingMemoController::class, 'review'])->name('meeting-memos.review');
        Route::get('/meeting-memos/{meetingMemo}/pdf', [\App\Http\Controllers\Admin\MeetingMemoController::class, 'downloadPdf'])->name('meeting-memos.pdf');
        Route::get('/attendance', [\App\Http\Controllers\Admin\AttendanceController::class, 'index'])->name('attendance.index');
        Route::get('/calendar', [\App\Http\Controllers\Admin\CalendarController::class, 'index'])->name('calendar.index');
    });

// ─── Manager ─────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'role:manager'])
    ->prefix('manager')
    ->name('manager.')
    ->group(function () {
        Route::get('/meeting-memos', [ManagerMeetingMemoController::class, 'index'])->name('meeting-memos.index');
        Route::get('/meeting-memos/{meetingMemo}', [ManagerMeetingMemoController::class, 'show'])->name('meeting-memos.show');
        Route::post('/meeting-memos/{meetingMemo}/review', [ManagerMeetingMemoController::class, 'review'])->name('meeting-memos.review');
        Route::get('/meeting-memos/{meetingMemo}/pdf', [ManagerMeetingMemoController::class, 'downloadPdf'])->name('meeting-memos.pdf');
        Route::get('/attendance', [\App\Http\Controllers\Manager\AttendanceController::class, 'index'])->name('attendance.index');
    });

// ─── Staff ──────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'verified', 'role:staff'])
    ->prefix('staff')
    ->name('staff.')
    ->group(function () {
        Route::get('/meeting-memos', [StaffMeetingMemoController::class, 'index'])->name('meeting-memos.index');
        Route::get('/meeting-memos/create', [StaffMeetingMemoController::class, 'create'])->name('meeting-memos.create');
        Route::post('/meeting-memos', [StaffMeetingMemoController::class, 'store'])->name('meeting-memos.store');
        Route::get('/meeting-memos/{meetingMemo}', [StaffMeetingMemoController::class, 'show'])->name('meeting-memos.show');
        Route::get('/meeting-memos/{meetingMemo}/edit', [StaffMeetingMemoController::class, 'edit'])->name('meeting-memos.edit');
        Route::match(['put', 'post'], '/meeting-memos/{meetingMemo}', [StaffMeetingMemoController::class, 'update'])->name('meeting-memos.update');
        Route::delete('/meeting-memos/{meetingMemo}', [StaffMeetingMemoController::class, 'destroy'])->name('meeting-memos.destroy');
        Route::post('/meeting-memos/{meetingMemo}/submit', [StaffMeetingMemoController::class, 'submit'])->name('meeting-memos.submit');
        Route::post('/meeting-memos/{meetingMemo}/duplicate', [StaffMeetingMemoController::class, 'duplicate'])->name('meeting-memos.duplicate');
        Route::delete('/meeting-memos/attachments/{attachment}', [\App\Http\Controllers\Staff\AttachmentController::class, 'destroy'])->name('meeting-memos.attachments.destroy');
        Route::get('/attendance', [\App\Http\Controllers\Staff\AttendanceController::class, 'index'])->name('attendance.index');
    });

require __DIR__.'/auth.php';
