<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PayrollPeriodController;
use App\Http\Controllers\PayrollEntryController;
use App\Http\Controllers\EmployeeLoanController;
use App\Http\Controllers\WpsExportController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->group(function () {
    // Payroll Periods
    Route::apiResource('payroll-periods', PayrollPeriodController::class);
    Route::post('payroll-periods/{payrollPeriod}/calculate', [PayrollPeriodController::class, 'calculate']);
    Route::post('payroll-periods/{payrollPeriod}/approve', [PayrollPeriodController::class, 'approve']);

    // Payroll Entries
    Route::apiResource('payroll-entries', PayrollEntryController::class)->except(['destroy']);
    Route::get('payroll-entries/{payrollEntry}/payslip', [PayrollEntryController::class, 'payslip']);

    // Employee Loans
    Route::apiResource('employee-loans', EmployeeLoanController::class);
    Route::post('employee-loans/{employeeLoan}/cancel', [EmployeeLoanController::class, 'cancel']);

    // WPS Export
    Route::post('payroll/wps-export', [WpsExportController::class, 'export']);
    Route::post('payroll/bank-transfer-list', [WpsExportController::class, 'generateBankTransferList']);
});
