<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EmployeeDocumentController;
use App\Http\Controllers\Api\EmployeeDependentController;
use App\Http\Controllers\Api\EmployeeQualificationController;
use App\Http\Controllers\Api\EmployeeWorkHistoryController;
use App\Http\Controllers\Api\EmployeeSkillController;
use App\Http\Controllers\Api\ProjectBudgetController;
use App\Http\Controllers\Api\CostCodeController;
use App\Http\Controllers\Api\ActualCostController;
use App\Http\Controllers\Api\CommittedCostController;
use App\Http\Controllers\Api\CostForecastController;
use App\Http\Controllers\Api\VarianceAnalysisController;
use App\Http\Controllers\Api\ProjectCostReportController;

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

    // Project Cost Control Module
    // Project Budgets
    Route::apiResource('project-budgets', ProjectBudgetController::class);
    Route::get('project-budgets/project/{projectId}', [ProjectBudgetController::class, 'byProject']);
    Route::post('project-budgets/{id}/approve', [ProjectBudgetController::class, 'approve']);
    Route::post('project-budgets/{id}/revise', [ProjectBudgetController::class, 'revise']);
    Route::get('project-budgets/{id}/items', [ProjectBudgetController::class, 'getItems']);
    Route::post('project-budgets/{id}/items', [ProjectBudgetController::class, 'updateItems']);
    Route::post('project-budgets/{id}/import-boq', [ProjectBudgetController::class, 'importFromBoq']);

    // Cost Codes
    Route::get('cost-codes/tree', [CostCodeController::class, 'tree']);
    Route::apiResource('cost-codes', CostCodeController::class);

    // Actual Costs
    Route::apiResource('actual-costs', ActualCostController::class);
    Route::get('actual-costs/project/{projectId}', [ActualCostController::class, 'byProject']);
    Route::post('actual-costs/import', [ActualCostController::class, 'import']);

    // Committed Costs
    Route::apiResource('committed-costs', CommittedCostController::class);
    Route::get('committed-costs/project/{projectId}', [CommittedCostController::class, 'byProject']);
    Route::post('committed-costs/sync-pos', [CommittedCostController::class, 'syncFromPurchaseOrders']);
    Route::post('committed-costs/sync-subcontracts', [CommittedCostController::class, 'syncFromSubcontracts']);

    // Cost Forecasts
    Route::apiResource('cost-forecasts', CostForecastController::class);
    Route::post('cost-forecasts/generate/{projectId}', [CostForecastController::class, 'generate']);
    Route::get('cost-forecasts/project/{projectId}', [CostForecastController::class, 'byProject']);

    // Variance Analysis
    Route::apiResource('variance-analysis', VarianceAnalysisController::class);
    Route::post('variance-analysis/analyze/{projectId}', [VarianceAnalysisController::class, 'analyze']);
    Route::get('variance-analysis/project/{projectId}', [VarianceAnalysisController::class, 'byProject']);

    // Project Cost Reports
    Route::get('project-cost-reports/cost-summary/{projectId}', [ProjectCostReportController::class, 'costSummary']);
    Route::get('project-cost-reports/budget-vs-actual/{projectId}', [ProjectCostReportController::class, 'budgetVsActual']);
    Route::get('project-cost-reports/cost-breakdown/{projectId}', [ProjectCostReportController::class, 'costBreakdown']);
    Route::get('project-cost-reports/commitment-status/{projectId}', [ProjectCostReportController::class, 'commitmentStatus']);
    Route::get('project-cost-reports/cost-trend/{projectId}', [ProjectCostReportController::class, 'costTrend']);
    Route::get('project-cost-reports/evm-analysis/{projectId}', [ProjectCostReportController::class, 'evmAnalysis']);
    Route::get('project-cost-reports/variance-report/{projectId}', [ProjectCostReportController::class, 'varianceReport']);
    Route::get('project-cost-reports/forecast-report/{projectId}', [ProjectCostReportController::class, 'forecastReport']);
    Route::get('project-cost-reports/cost-to-complete/{projectId}', [ProjectCostReportController::class, 'costToComplete']);
    Route::post('project-cost-reports/generate-monthly/{projectId}', [ProjectCostReportController::class, 'generateMonthlyReport']);
    Route::apiResource('project-cost-reports', ProjectCostReportController::class);
});
