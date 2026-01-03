<?php

use App\Http\Controllers\Api\ReportsController;
use App\Http\Controllers\Api\ReportExportController;
use App\Http\Controllers\Api\ReportScheduleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Report Generation Routes
Route::prefix('reports')->group(function () {
    Route::post('/trial-balance', [ReportsController::class, 'trialBalance']);
    Route::post('/balance-sheet', [ReportsController::class, 'balanceSheet']);
    Route::post('/income-statement', [ReportsController::class, 'incomeStatement']);
    Route::post('/cash-flow', [ReportsController::class, 'cashFlow']);
    Route::post('/general-ledger', [ReportsController::class, 'generalLedger']);
    Route::post('/account-transactions', [ReportsController::class, 'accountTransactions']);
    Route::post('/ap-aging', [ReportsController::class, 'apAging']);
    Route::post('/ar-aging', [ReportsController::class, 'arAging']);
    Route::post('/vendor-statement', [ReportsController::class, 'vendorStatement']);
    Route::post('/customer-statement', [ReportsController::class, 'customerStatement']);
    Route::post('/project-profitability', [ReportsController::class, 'projectProfitability']);
    Route::post('/cost-center', [ReportsController::class, 'costCenter']);
    Route::post('/budget-vs-actual', [ReportsController::class, 'budgetVsActual']);
    Route::post('/payment-analysis', [ReportsController::class, 'paymentAnalysis']);
    Route::post('/tax-report', [ReportsController::class, 'taxReport']);
    
    // Export routes
    Route::post('/export', [ReportExportController::class, 'export']);
    Route::get('/{reportId}/export', [ReportExportController::class, 'export']);
    
    // Drill-down route
    Route::get('/drill-down', [ReportsController::class, 'drillDown']);
});

// Report History Routes
Route::get('/report-history', [ReportExportController::class, 'index']);
Route::get('/report-history/{id}/download', [ReportExportController::class, 'download']);

// Report Schedules Routes
Route::apiResource('report-schedules', ReportScheduleController::class);

