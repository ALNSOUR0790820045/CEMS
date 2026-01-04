<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\LeaveRequestController;
use App\Http\Controllers\Api\ShiftScheduleController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\EmployeeController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| 
| Note: These routes should be protected with authentication middleware
| in production. Add 'auth:sanctum' or appropriate middleware as needed:
| Route::middleware(['auth:sanctum'])->group(function () { ... });
|
*/

Route::prefix('api')->middleware('api')->group(function () {
    
    // Employee Routes
    Route::prefix('employees')->group(function () {
        Route::get('/', [EmployeeController::class, 'index']);
        Route::post('/', [EmployeeController::class, 'store']);
        Route::get('/{id}', [EmployeeController::class, 'show']);
        Route::put('/{id}', [EmployeeController::class, 'update']);
        Route::delete('/{id}', [EmployeeController::class, 'destroy']);
    });
    
    // Attendance Routes
    Route::prefix('attendance')->group(function () {
        Route::get('/', [AttendanceController::class, 'index']);
        Route::post('/', [AttendanceController::class, 'store']);
        Route::get('/{id}', [AttendanceController::class, 'show']);
        Route::put('/{id}', [AttendanceController::class, 'update']);
        Route::delete('/{id}', [AttendanceController::class, 'destroy']);
        Route::post('/check-in', [AttendanceController::class, 'checkIn']);
        Route::post('/check-out', [AttendanceController::class, 'checkOut']);
    });

    // Leave Request Routes
    Route::prefix('leave-requests')->group(function () {
        Route::get('/', [LeaveRequestController::class, 'index']);
        Route::post('/', [LeaveRequestController::class, 'store']);
        Route::get('/{id}', [LeaveRequestController::class, 'show']);
        Route::put('/{id}', [LeaveRequestController::class, 'update']);
        Route::delete('/{id}', [LeaveRequestController::class, 'destroy']);
        Route::post('/{id}/approve', [LeaveRequestController::class, 'approve']);
        Route::post('/{id}/reject', [LeaveRequestController::class, 'reject']);
        Route::post('/{id}/cancel', [LeaveRequestController::class, 'cancel']);
    });

    // Shift Schedule Routes
    Route::prefix('shift-schedules')->group(function () {
        Route::get('/', [ShiftScheduleController::class, 'index']);
        Route::post('/', [ShiftScheduleController::class, 'store']);
        Route::get('/{id}', [ShiftScheduleController::class, 'show']);
        Route::put('/{id}', [ShiftScheduleController::class, 'update']);
        Route::delete('/{id}', [ShiftScheduleController::class, 'destroy']);
    });

    // Reports Routes
    Route::prefix('reports')->group(function () {
        Route::get('/attendance-summary', [ReportController::class, 'attendanceSummary']);
        Route::get('/daily-attendance', [ReportController::class, 'dailyAttendance']);
        Route::get('/monthly-attendance', [ReportController::class, 'monthlyAttendance']);
        Route::get('/leave-report', [ReportController::class, 'leaveReport']);
        Route::get('/overtime-report', [ReportController::class, 'overtimeReport']);
    });
});
