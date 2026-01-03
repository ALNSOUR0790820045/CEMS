<?php

use App\Http\Controllers\Api\AgedReportController;
use App\Http\Controllers\Api\CustomReportController;
use App\Http\Controllers\Api\ProjectReportController;
use App\Http\Controllers\Api\ReportController;
use Illuminate\Support\Facades\Route;

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

Route::middleware(['auth:sanctum'])->group(function () {
    // Core Financial Reports
    Route::prefix('reports')->group(function () {
        Route::get('/trial-balance', [ReportController::class, 'trialBalance']);
        Route::get('/balance-sheet', [ReportController::class, 'balanceSheet']);
        Route::get('/income-statement', [ReportController::class, 'incomeStatement']);
        Route::get('/cash-flow', [ReportController::class, 'cashFlow']);
        Route::get('/general-ledger', [ReportController::class, 'generalLedger']);
        Route::get('/account-statement', [ReportController::class, 'accountStatement']);

        // Aged Reports
        Route::get('/ap-aging', [AgedReportController::class, 'accountsPayableAging']);
        Route::get('/ar-aging', [AgedReportController::class, 'accountsReceivableAging']);
        Route::get('/vendor-outstanding', [AgedReportController::class, 'vendorOutstanding']);
        Route::get('/customer-outstanding', [AgedReportController::class, 'customerOutstanding']);

        // Project Financial Reports
        Route::get('/project-profitability', [ProjectReportController::class, 'profitability']);
        Route::get('/project-cost-analysis', [ProjectReportController::class, 'costAnalysis']);
        Route::get('/budget-vs-actual', [ProjectReportController::class, 'budgetVsActual']);
        Route::get('/project-cash-flow', [ProjectReportController::class, 'cashFlow']);
        Route::get('/cost-performance-index', [ProjectReportController::class, 'costPerformanceIndex']);

        // Management Reports
        Route::get('/executive-dashboard', [ReportController::class, 'executiveDashboard']);
        Route::get('/kpi-metrics', [ReportController::class, 'kpiMetrics']);
        Route::get('/revenue-analysis', [ReportController::class, 'revenueAnalysis']);
        Route::get('/expense-analysis', [ReportController::class, 'expenseAnalysis']);
        Route::get('/profitability-analysis', [ReportController::class, 'profitabilityAnalysis']);

        // Tax & Compliance
        Route::get('/vat-report', [ReportController::class, 'vatReport']);
        Route::get('/withholding-tax-report', [ReportController::class, 'withholdingTaxReport']);
        Route::get('/audit-trail', [ReportController::class, 'auditTrail']);

        // Custom Report Builder
        Route::post('/custom', [CustomReportController::class, 'generate']);
    });
});
