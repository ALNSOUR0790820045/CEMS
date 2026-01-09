<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EmployeeDocumentController;
use App\Http\Controllers\Api\EmployeeDependentController;
use App\Http\Controllers\Api\EmployeeQualificationController;
use App\Http\Controllers\Api\EmployeeWorkHistoryController;
use App\Http\Controllers\Api\EmployeeSkillController;
use App\Http\Controllers\RiskRegisterController;
use App\Http\Controllers\RiskController;
use App\Http\Controllers\RiskCategoryController;
use App\Http\Controllers\RiskAssessmentController;
use App\Http\Controllers\RiskResponseController;
use App\Http\Controllers\RiskMonitoringController;
use App\Http\Controllers\RiskIncidentController;
use App\Http\Controllers\RiskReportController;

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

    // Risk Management Module
    // Risk Registers
    Route::apiResource('risk-registers', RiskRegisterController::class);
    Route::get('risk-registers/project/{projectId}', [RiskRegisterController::class, 'byProject']);
    Route::post('risk-registers/{id}/approve', [RiskRegisterController::class, 'approve']);

    // Risks
    Route::apiResource('risks', RiskController::class);
    Route::get('risks/project/{projectId}', [RiskController::class, 'byProject']);
    Route::get('risks/register/{registerId}', [RiskController::class, 'byRegister']);
    Route::post('risks/{id}/assess', [RiskController::class, 'assess']);
    Route::post('risks/{id}/respond', [RiskController::class, 'addResponse']);
    Route::post('risks/{id}/monitor', [RiskController::class, 'monitor']);
    Route::post('risks/{id}/close', [RiskController::class, 'close']);
    Route::post('risks/{id}/escalate', [RiskController::class, 'escalate']);

    // Risk Categories
    Route::apiResource('risk-categories', RiskCategoryController::class);
    Route::get('risk-categories/tree', [RiskCategoryController::class, 'tree']);

    // Risk Assessments
    Route::get('risks/{riskId}/assessments', [RiskAssessmentController::class, 'byRisk']);
    Route::post('risks/{riskId}/assessments', [RiskAssessmentController::class, 'store']);

    // Risk Responses
    Route::get('risks/{riskId}/responses', [RiskResponseController::class, 'byRisk']);
    Route::post('risks/{riskId}/responses', [RiskResponseController::class, 'store']);
    Route::post('risk-responses/{id}/complete', [RiskResponseController::class, 'complete']);

    // Risk Monitoring
    Route::get('risks/{riskId}/monitoring', [RiskMonitoringController::class, 'byRisk']);
    Route::post('risks/{riskId}/monitoring', [RiskMonitoringController::class, 'store']);

    // Risk Incidents
    Route::apiResource('risk-incidents', RiskIncidentController::class);
    Route::post('risk-incidents/{id}/resolve', [RiskIncidentController::class, 'resolve']);

    // Risk Reports
    Route::get('reports/risk-summary/{projectId}', [RiskReportController::class, 'summary']);
    Route::get('reports/risk-matrix/{projectId}', [RiskReportController::class, 'riskMatrix']);
    Route::get('reports/risk-heat-map/{projectId}', [RiskReportController::class, 'heatMap']);
    Route::get('reports/risk-trend/{projectId}', [RiskReportController::class, 'trend']);
    Route::get('reports/top-risks/{projectId}', [RiskReportController::class, 'topRisks']);
    Route::get('reports/risk-exposure/{projectId}', [RiskReportController::class, 'exposure']);
    Route::get('reports/response-status/{projectId}', [RiskReportController::class, 'responseStatus']);
});
