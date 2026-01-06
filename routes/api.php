<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\WarehouseController;
use App\Http\Controllers\Api\WarehouseLocationController;
use App\Http\Controllers\Api\WarehouseStockController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\LeaveRequestController;
use App\Http\Controllers\Api\ShiftScheduleController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\BranchController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.   Make something great!
|
*/

Route::middleware('auth: sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum'])->group(function () {
    // Documents Management API
    Route::prefix('documents')->group(function () {
        Route::get('/', [DocumentController::class, 'index']);
        Route::post('/', [DocumentController::class, 'store']);
        Route::get('/search', [DocumentController:: class, 'search']);
        Route::get('/{document}', [DocumentController::class, 'show']);
        Route::put('/{document}', [DocumentController:: class, 'update']);
        Route::patch('/{document}', [DocumentController::class, 'update']);
        Route::delete('/{document}', [DocumentController:: class, 'destroy']);
        Route::post('/{document}/upload-version', [DocumentController::class, 'uploadVersion']);
        Route::get('/{document}/versions', [DocumentController::class, 'versions']);
        Route::post('/{document}/grant-access', [DocumentController::class, 'grantAccess']);
    });
    
    // Warehouse Management API Routes
    Route::apiResource('warehouses', WarehouseController::class);
    Route::apiResource('warehouse-locations', WarehouseLocationController::class);
    
    Route::get('warehouse-stock', [WarehouseStockController:: class, 'index']);
    Route::get('warehouse-stock/availability', [WarehouseStockController::class, 'availability']);
    Route::post('warehouse-stock/transfer', [WarehouseStockController::class, 'transfer']);
    
    // Branch Management API
    Route::apiResource('branches', BranchController::class);
    Route::get('branches/{branch}/users', [BranchController::class, 'users']);
    
    // Employee Management API
    Route::prefix('employees')->group(function () {
        Route::get('/', [EmployeeController::class, 'index']);
        Route::post('/', [EmployeeController::class, 'store']);
        Route::get('/{id}', [EmployeeController:: class, 'show']);
        Route::put('/{id}', [EmployeeController::class, 'update']);
        Route::delete('/{id}', [EmployeeController::class, 'destroy']);
    });
    
    // Attendance Management API
    Route::prefix('attendance')->group(function () {
        Route::get('/', [AttendanceController:: class, 'index']);
        Route::post('/', [AttendanceController::class, 'store']);
        Route::get('/{id}', [AttendanceController::class, 'show']);
        Route::put('/{id}', [AttendanceController::class, 'update']);
        Route::delete('/{id}', [AttendanceController::class, 'destroy']);
        Route::post('/check-in', [AttendanceController::class, 'checkIn']);
        Route::post('/check-out', [AttendanceController::class, 'checkOut']);
    });

    // Leave Request Management API
    Route::prefix('leave-requests')->group(function () {
        Route::get('/', [LeaveRequestController::class, 'index']);
        Route::post('/', [LeaveRequestController::class, 'store']);
        Route::get('/{id}', [LeaveRequestController:: class, 'show']);
        Route::put('/{id}', [LeaveRequestController::class, 'update']);
        Route::delete('/{id}', [LeaveRequestController::class, 'destroy']);
        Route::post('/{id}/approve', [LeaveRequestController::class, 'approve']);
        Route::post('/{id}/reject', [LeaveRequestController::class, 'reject']);
        Route::post('/{id}/cancel', [LeaveRequestController::class, 'cancel']);
    });

    // Shift Schedule Management API
    Route::prefix('shift-schedules')->group(function () {
        Route::get('/', [ShiftScheduleController:: class, 'index']);
        Route::post('/', [ShiftScheduleController::class, 'store']);
        Route::get('/{id}', [ShiftScheduleController::class, 'show']);
        Route::put('/{id}', [ShiftScheduleController::class, 'update']);
        Route::delete('/{id}', [ShiftScheduleController::class, 'destroy']);
    });

    // Reports API
    Route::prefix('reports')->group(function () {
        Route::get('/attendance-summary', [ReportController::class, 'attendanceSummary']);
        Route::get('/daily-attendance', [ReportController::class, 'dailyAttendance']);
        Route::get('/monthly-attendance', [ReportController::class, 'monthlyAttendance']);
        Route::get('/leave-report', [ReportController::class, 'leaveReport']);
        Route::get('/overtime-report', [ReportController::class, 'overtimeReport']);
    });
});