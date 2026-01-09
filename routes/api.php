<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EmployeeDocumentController;
use App\Http\Controllers\Api\EmployeeDependentController;
use App\Http\Controllers\Api\EmployeeQualificationController;
use App\Http\Controllers\Api\EmployeeWorkHistoryController;
use App\Http\Controllers\Api\EmployeeSkillController;
use App\Http\Controllers\Api\InspectionTypeController;
use App\Http\Controllers\Api\InspectionRequestController;
use App\Http\Controllers\Api\InspectionController;
use App\Http\Controllers\Api\InspectionActionController;
use App\Http\Controllers\Api\InspectionTemplateController;
use App\Http\Controllers\Api\InspectionReportController;

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

    // Inspection Types
    Route::apiResource('inspection-types', InspectionTypeController::class);

    // Inspection Requests
    Route::apiResource('inspection-requests', InspectionRequestController::class);
    Route::get('inspection-requests/project/{projectId}', [InspectionRequestController::class, 'byProject']);
    Route::post('inspection-requests/{id}/schedule', [InspectionRequestController::class, 'schedule']);
    Route::post('inspection-requests/{id}/cancel', [InspectionRequestController::class, 'cancel']);
    Route::post('inspection-requests/{id}/reject', [InspectionRequestController::class, 'reject']);

    // Inspections
    Route::apiResource('inspections', InspectionController::class);
    Route::get('inspections/project/{projectId}', [InspectionController::class, 'byProject']);
    Route::get('inspections/{id}/items', [InspectionController::class, 'getItems']);
    Route::post('inspections/{id}/items', [InspectionController::class, 'saveItems']);
    Route::post('inspections/{id}/submit', [InspectionController::class, 'submit']);
    Route::post('inspections/{id}/approve', [InspectionController::class, 'approve']);
    Route::post('inspections/{id}/reject', [InspectionController::class, 'reject']);
    Route::post('inspections/{id}/reinspect', [InspectionController::class, 'createReinspection']);

    // Inspection Actions
    Route::get('inspections/{id}/actions', [InspectionActionController::class, 'byInspection']);
    Route::post('inspections/{id}/actions', [InspectionActionController::class, 'store']);
    Route::post('inspection-actions/{id}/complete', [InspectionActionController::class, 'complete']);
    Route::post('inspection-actions/{id}/verify', [InspectionActionController::class, 'verify']);

    // Inspection Templates
    Route::apiResource('inspection-templates', InspectionTemplateController::class);
    Route::post('inspection-templates/{id}/duplicate', [InspectionTemplateController::class, 'duplicate']);
    Route::get('inspection-templates/{id}/items', [InspectionTemplateController::class, 'getItems']);

    // Inspection Reports
    Route::get('reports/inspection-summary/{projectId}', [InspectionReportController::class, 'summary']);
    Route::get('reports/inspection-log/{projectId}', [InspectionReportController::class, 'log']);
    Route::get('reports/pass-rate/{projectId}', [InspectionReportController::class, 'passRate']);
    Route::get('reports/pending-actions/{projectId}', [InspectionReportController::class, 'pendingActions']);
    Route::get('reports/inspector-performance', [InspectionReportController::class, 'inspectorPerformance']);
    Route::get('reports/defect-analysis/{projectId}', [InspectionReportController::class, 'defectAnalysis']);
});
