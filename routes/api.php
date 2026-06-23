<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\LeaveRequestController;
use App\Http\Controllers\Api\ApprovalController;
use App\Http\Controllers\Api\MasterDataController;
use App\Http\Controllers\Api\LeaveBalanceController;
use Illuminate\Support\Facades\Route;

// Public
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Master Data (public for now, can add permission later)
Route::get('/divisions', [MasterDataController::class, 'divisions']);
Route::get('/positions', [MasterDataController::class, 'positions']);
Route::get('/shifts', [MasterDataController::class, 'shifts']);
Route::get('/work-locations', [MasterDataController::class, 'workLocations']);
Route::get('/leave-types', [MasterDataController::class, 'leaveTypes']);

// Protected
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    Route::put('/profile', [ProfileController::class, 'update']);
    Route::put('/profile/password', [ProfileController::class, 'changePassword']);

    // Attendance
    Route::get('/attendance/today', [AttendanceController::class, 'today']);
    Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn']);
    Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut']);
    Route::get('/attendance/history', [AttendanceController::class, 'history']);
    Route::post('/attendance/correction', [AttendanceController::class, 'correction']);

    // Leave Requests
    Route::get('/leave-requests', [LeaveRequestController::class, 'index']);
    Route::post('/leave-requests', [LeaveRequestController::class, 'store']);
    Route::put('/leave-requests/{leaveRequest}/cancel', [LeaveRequestController::class, 'cancel']);

    // Leave Balances
    Route::get('/leave-balances', [LeaveBalanceController::class, 'index']);

    // Approvals
    Route::get('/approvals/pending', [ApprovalController::class, 'pending']);
    Route::post('/approvals/{approval}/approve', [ApprovalController::class, 'approve']);
    Route::post('/approvals/{approval}/reject', [ApprovalController::class, 'reject']);

    // Employees (for approval chain selection)
    Route::get('/employees', [MasterDataController::class, 'employees']);
});
