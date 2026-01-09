<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EmployeeDocumentController;
use App\Http\Controllers\Api\EmployeeDependentController;
use App\Http\Controllers\Api\EmployeeQualificationController;
use App\Http\Controllers\Api\EmployeeWorkHistoryController;
use App\Http\Controllers\Api\EmployeeSkillController;
use App\Http\Controllers\RetentionController;
use App\Http\Controllers\RetentionReleaseController;
use App\Http\Controllers\RetentionGuaranteeController;
use App\Http\Controllers\AdvancePaymentController;
use App\Http\Controllers\DefectsLiabilityController;
use App\Http\Controllers\DefectNotificationController;
use App\Http\Controllers\RetentionReportController;

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

    // Retention Management
    Route::apiResource('retentions', RetentionController::class);
    Route::get('retentions/project/{projectId}', [RetentionController::class, 'byProject']);
    Route::get('retentions/{id}/accumulations', [RetentionController::class, 'getAccumulations']);
    Route::get('retentions/{id}/releases', [RetentionController::class, 'getReleases']);
    Route::get('retentions/{id}/statement', [RetentionController::class, 'statement']);
    Route::post('retentions/{id}/calculate', [RetentionController::class, 'calculate']);

    // Retention Releases
    Route::apiResource('retention-releases', RetentionReleaseController::class);
    Route::post('retention-releases/{id}/approve', [RetentionReleaseController::class, 'approve']);
    Route::post('retention-releases/{id}/release', [RetentionReleaseController::class, 'release']);
    Route::post('retention-releases/{id}/mark-paid', [RetentionReleaseController::class, 'markPaid']);

    // Retention Guarantees
    Route::apiResource('retention-guarantees', RetentionGuaranteeController::class);
    Route::post('retention-guarantees/{id}/release', [RetentionGuaranteeController::class, 'release']);
    Route::get('retention-guarantees/expiring', [RetentionGuaranteeController::class, 'expiring']);

    // Advance Payments
    Route::apiResource('advance-payments', AdvancePaymentController::class);
    Route::get('advance-payments/{id}/recoveries', [AdvancePaymentController::class, 'getRecoveries']);
    Route::post('advance-payments/{id}/approve', [AdvancePaymentController::class, 'approve']);
    Route::post('advance-payments/{id}/pay', [AdvancePaymentController::class, 'pay']);
    Route::get('advance-payments/{id}/statement', [AdvancePaymentController::class, 'statement']);

    // Defects Liability
    Route::apiResource('defects-liability', DefectsLiabilityController::class);
    Route::get('defects-liability/{id}/notifications', [DefectsLiabilityController::class, 'getNotifications']);
    Route::post('defects-liability/{id}/extend', [DefectsLiabilityController::class, 'extend']);
    Route::post('defects-liability/{id}/complete', [DefectsLiabilityController::class, 'complete']);

    // Defect Notifications
    Route::apiResource('defect-notifications', DefectNotificationController::class);
    Route::post('defect-notifications/{id}/acknowledge', [DefectNotificationController::class, 'acknowledge']);
    Route::post('defect-notifications/{id}/rectify', [DefectNotificationController::class, 'rectify']);

    // Retention Reports
    Route::get('reports/retention-summary', [RetentionReportController::class, 'summary']);
    Route::get('reports/retention-aging', [RetentionReportController::class, 'aging']);
    Route::get('reports/advance-balance', [RetentionReportController::class, 'advanceBalance']);
    Route::get('reports/dlp-status', [RetentionReportController::class, 'dlpStatus']);
    Route::get('reports/guarantee-expiry', [RetentionReportController::class, 'guaranteeExpiry']);
    Route::get('reports/retention-forecast', [RetentionReportController::class, 'releaseForecast']);
});
