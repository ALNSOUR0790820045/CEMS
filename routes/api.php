<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EmployeeDocumentController;
use App\Http\Controllers\Api\EmployeeDependentController;
use App\Http\Controllers\Api\EmployeeQualificationController;
use App\Http\Controllers\Api\EmployeeWorkHistoryController;
use App\Http\Controllers\Api\EmployeeSkillController;
use App\Http\Controllers\ProgressBillController;
use App\Http\Controllers\MeasurementSheetController;
use App\Http\Controllers\BillApprovalController;
use App\Http\Controllers\BillReportController;

Route::middleware('auth:sanctum')->group(function () {
    // Employee Documents
    Route::prefix('employees/{employee}')->group(function () {
        Route::apiResource('documents', EmployeeDocumentController::class);
        Route::get('documents/{document}/download', [EmployeeDocumentController::class, 'download'])
            ->name('api.employees.documents.download');
        
        Route::apiResource('dependents', EmployeeDependentController::class);
        Route::apiResource('qualifications', EmployeeQualificationController::class);
        Route::apiResource('work-history', EmployeeWorkHistoryController::class);
        Route::apiResource('skills', EmployeeSkillController::class);
    });

    // Progress Bills
    Route::apiResource('progress-bills', ProgressBillController::class);
    Route::get('progress-bills/project/{projectId}', [ProgressBillController::class, 'byProject']);
    Route::get('progress-bills/{id}/preview', [ProgressBillController::class, 'preview']);
    Route::post('progress-bills/{id}/submit', [ProgressBillController::class, 'submit']);
    Route::post('progress-bills/{id}/review', [ProgressBillController::class, 'review']);
    Route::post('progress-bills/{id}/certify', [ProgressBillController::class, 'certify']);
    Route::post('progress-bills/{id}/approve', [ProgressBillController::class, 'approve']);
    Route::post('progress-bills/{id}/reject', [ProgressBillController::class, 'reject']);
    Route::post('progress-bills/{id}/mark-paid', [ProgressBillController::class, 'markPaid']);
    Route::post('progress-bills/create-next/{projectId}', [ProgressBillController::class, 'createNext']);

    // Bill Items
    Route::get('progress-bills/{id}/items', [ProgressBillController::class, 'getItems']);
    Route::post('progress-bills/{id}/items', [ProgressBillController::class, 'updateItems']);
    Route::post('progress-bills/{id}/items/import-boq', [ProgressBillController::class, 'importFromBoq']);
    Route::post('progress-bills/{id}/items/calculate', [ProgressBillController::class, 'calculateItems']);

    // Variations
    Route::get('progress-bills/{id}/variations', [ProgressBillController::class, 'getVariations']);
    Route::post('progress-bills/{id}/variations', [ProgressBillController::class, 'addVariation']);

    // Deductions
    Route::get('progress-bills/{id}/deductions', [ProgressBillController::class, 'getDeductions']);
    Route::post('progress-bills/{id}/deductions', [ProgressBillController::class, 'addDeduction']);
    Route::post('progress-bills/{id}/calculate-deductions', [ProgressBillController::class, 'calculateDeductions']);

    // Measurement Sheets
    Route::apiResource('measurement-sheets', MeasurementSheetController::class);
    Route::get('measurement-sheets/bill/{billId}', [MeasurementSheetController::class, 'byBill']);
    Route::post('measurement-sheets/{id}/approve', [MeasurementSheetController::class, 'approve']);

    // Bill Approval Workflow
    Route::apiResource('bill-approvals', BillApprovalController::class)->only(['index', 'store', 'show']);
    Route::get('bill-approvals/bill/{billId}', [BillApprovalController::class, 'getByBill']);

    // Reports
    Route::get('reports/billing-summary/{projectId}', [BillReportController::class, 'billingSummary']);
    Route::get('reports/payment-status/{projectId}', [BillReportController::class, 'paymentStatus']);
    Route::get('reports/retention-summary/{projectId}', [BillReportController::class, 'retentionSummary']);
    Route::get('reports/billing-forecast/{projectId}', [BillReportController::class, 'billingForecast']);
    Route::get('reports/cash-flow/{projectId}', [BillReportController::class, 'cashFlow']);
    Route::get('reports/aging-report', [BillReportController::class, 'agingReport']);
});
