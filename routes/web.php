<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ApprovalChainController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\LeaveTypeController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\ScheduleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::match(['GET', 'POST'], '/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // Users
    Route::get('users', [AdminController::class, 'users'])->name('admin.users');
    Route::get('users/create', [AdminController::class, 'createUser'])->name('admin.users.create');
    Route::post('users', [AdminController::class, 'storeUser'])->name('admin.users.store');
    Route::get('users/{user}/edit', [AdminController::class, 'editUser'])->name('admin.users.edit');
    Route::put('users/{user}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('users/{user}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');

    // Divisions
    Route::get('divisions', [DivisionController::class, 'index'])->name('admin.divisions');
    Route::get('divisions/create', [DivisionController::class, 'create'])->name('admin.divisions.create');
    Route::post('divisions', [DivisionController::class, 'store'])->name('admin.divisions.store');
    Route::get('divisions/{division}/edit', [DivisionController::class, 'edit'])->name('admin.divisions.edit');
    Route::put('divisions/{division}', [DivisionController::class, 'update'])->name('admin.divisions.update');
    Route::delete('divisions/{division}', [DivisionController::class, 'destroy'])->name('admin.divisions.delete');

    // Positions
    Route::get('positions', [PositionController::class, 'index'])->name('admin.positions');
    Route::get('positions/create', [PositionController::class, 'create'])->name('admin.positions.create');
    Route::post('positions', [PositionController::class, 'store'])->name('admin.positions.store');
    Route::get('positions/{position}/edit', [PositionController::class, 'edit'])->name('admin.positions.edit');
    Route::put('positions/{position}', [PositionController::class, 'update'])->name('admin.positions.update');
    Route::delete('positions/{position}', [PositionController::class, 'destroy'])->name('admin.positions.delete');

    // Shifts
    Route::get('shifts', [MasterDataController::class, 'shifts'])->name('admin.shifts');
    Route::post('shifts', [MasterDataController::class, 'storeShift'])->name('admin.shifts.store');
    Route::put('shifts/{shift}', [MasterDataController::class, 'updateShift'])->name('admin.shifts.update');
    Route::delete('shifts/{shift}', [MasterDataController::class, 'destroyShift'])->name('admin.shifts.delete');

    // Locations
    Route::get('locations', [MasterDataController::class, 'locations'])->name('admin.locations');
    Route::post('locations', [MasterDataController::class, 'storeLocation'])->name('admin.locations.store');
    Route::put('locations/{location}', [MasterDataController::class, 'updateLocation'])->name('admin.locations.update');
    Route::delete('locations/{location}', [MasterDataController::class, 'destroyLocation'])->name('admin.locations.delete');

    // Holidays
    Route::get('holidays', [MasterDataController::class, 'holidays'])->name('admin.holidays');
    Route::post('holidays', [MasterDataController::class, 'storeHoliday'])->name('admin.holidays.store');
    Route::delete('holidays/{holiday}', [MasterDataController::class, 'destroyHoliday'])->name('admin.holidays.delete');

    // Approval Chains
    Route::get('approval-chains', [ApprovalChainController::class, 'index'])->name('admin.approval-chains');
    Route::get('approval-chains/create', [ApprovalChainController::class, 'create'])->name('admin.approval-chains.create');
    Route::post('approval-chains', [ApprovalChainController::class, 'store'])->name('admin.approval-chains.store');
    Route::get('approval-chains/{approvalChain}/edit', [ApprovalChainController::class, 'edit'])->name('admin.approval-chains.edit');
    Route::put('approval-chains/{approvalChain}', [ApprovalChainController::class, 'update'])->name('admin.approval-chains.update');
    Route::delete('approval-chains/{approvalChain}', [ApprovalChainController::class, 'destroy'])->name('admin.approval-chains.delete');

    // Leave Types
    Route::get('leave-types', [LeaveTypeController::class, 'index'])->name('admin.leave-types');
    Route::get('leave-types/create', [LeaveTypeController::class, 'create'])->name('admin.leave-types.create');
    Route::post('leave-types', [LeaveTypeController::class, 'store'])->name('admin.leave-types.store');
    Route::get('leave-types/{leaveType}/edit', [LeaveTypeController::class, 'edit'])->name('admin.leave-types.edit');
    Route::put('leave-types/{leaveType}', [LeaveTypeController::class, 'update'])->name('admin.leave-types.update');
    Route::delete('leave-types/{leaveType}', [LeaveTypeController::class, 'destroy'])->name('admin.leave-types.delete');

    // Schedules
    Route::get('schedules', [ScheduleController::class, 'index'])->name('admin.schedules');
    Route::post('schedules/generate', [ScheduleController::class, 'generate'])->name('admin.schedules.generate');
    Route::put('schedules/{schedule}', [ScheduleController::class, 'update'])->name('admin.schedules.update');
    Route::post('schedules/clear', [ScheduleController::class, 'clear'])->name('admin.schedules.clear');
    Route::get('schedules/user/{user}', [ScheduleController::class, 'userSchedule'])->name('admin.schedules.user');
    Route::post('schedules/user/{user}/update', [ScheduleController::class, 'userScheduleUpdate'])->name('admin.schedules.user.update');
});

Route::get('/dashboard', function () {
    return redirect('/admin/dashboard');
})->middleware('auth')->name('dashboard');
