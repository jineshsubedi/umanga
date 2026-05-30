<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Authentication
Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);
    Route::get('/user', [\App\Http\Controllers\Api\AuthController::class, 'user']);

    // Profile
    Route::get('/profile', [\App\Http\Controllers\Api\ProfileController::class, 'show']);
    Route::post('/profile', [\App\Http\Controllers\Api\ProfileController::class, 'update']);
    Route::delete('/profile', [\App\Http\Controllers\Api\ProfileController::class, 'destroy']);

    // Notifications
    Route::get('/notifications', [\App\Http\Controllers\Api\NotificationController::class, 'index']);
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\Api\NotificationController::class, 'markAllRead']);
    Route::post('/notifications/{id}/mark-read', [\App\Http\Controllers\Api\NotificationController::class, 'markRead']);

    // Common Attendance
    Route::post('/attendance/clock-in', [\App\Http\Controllers\Api\AttendanceController::class, 'clockIn']);
    Route::post('/attendance/clock-out', [\App\Http\Controllers\Api\AttendanceController::class, 'clockOut']);

    // ─── Admin ───────────────────────────────────────────────────────────────────
    Route::middleware(['role:admin'])->prefix('admin')->name('api.admin.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Api\Admin\DashboardController::class, 'index']);
        
        Route::apiResource('users', \App\Http\Controllers\Api\Admin\UserController::class);
        Route::patch('/users/{user}/toggle-status', [\App\Http\Controllers\Api\Admin\UserController::class, 'toggleStatus']);

        Route::get('/meeting-memos', [\App\Http\Controllers\Api\Admin\MeetingMemoController::class, 'index']);
        Route::get('/meeting-memos/{meetingMemo}', [\App\Http\Controllers\Api\Admin\MeetingMemoController::class, 'show']);
        Route::post('/meeting-memos/{meetingMemo}/review', [\App\Http\Controllers\Api\Admin\MeetingMemoController::class, 'review']);

        Route::get('/attendance', [\App\Http\Controllers\Api\Admin\AttendanceController::class, 'index']);
    });

    // ─── Manager ─────────────────────────────────────────────────────────────────
    Route::middleware(['role:manager'])->prefix('manager')->name('api.manager.')->group(function () {
        Route::get('/meeting-memos', [\App\Http\Controllers\Api\Manager\MeetingMemoController::class, 'index']);
        Route::get('/meeting-memos/{meetingMemo}', [\App\Http\Controllers\Api\Manager\MeetingMemoController::class, 'show']);
        Route::post('/meeting-memos/{meetingMemo}/review', [\App\Http\Controllers\Api\Manager\MeetingMemoController::class, 'review']);

        Route::get('/attendance', [\App\Http\Controllers\Api\Manager\AttendanceController::class, 'index']);
    });

    // ─── Staff ──────────────────────────────────────────────────────────────────
    Route::middleware(['role:staff'])->prefix('staff')->name('api.staff.')->group(function () {
        Route::apiResource('meeting-memos', \App\Http\Controllers\Api\Staff\MeetingMemoController::class)->except(['create', 'edit']);
        Route::post('/meeting-memos/{meetingMemo}', [\App\Http\Controllers\Api\Staff\MeetingMemoController::class, 'update']); // for multipart form data support
        Route::post('/meeting-memos/{meetingMemo}/submit', [\App\Http\Controllers\Api\Staff\MeetingMemoController::class, 'submit']);
        Route::post('/meeting-memos/{meetingMemo}/duplicate', [\App\Http\Controllers\Api\Staff\MeetingMemoController::class, 'duplicate']);
        Route::delete('/meeting-memos/attachments/{attachment}', [\App\Http\Controllers\Api\Staff\MeetingMemoController::class, 'destroyAttachment']);

        Route::get('/attendance', [\App\Http\Controllers\Api\Staff\AttendanceController::class, 'index']);
    });
});
