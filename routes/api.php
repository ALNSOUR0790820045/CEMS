<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EmployeeDocumentController;
use App\Http\Controllers\Api\EmployeeDependentController;
use App\Http\Controllers\Api\EmployeeQualificationController;
use App\Http\Controllers\Api\EmployeeWorkHistoryController;
use App\Http\Controllers\Api\EmployeeSkillController;
use App\Http\Controllers\Api\PunchListController;
use App\Http\Controllers\Api\PunchItemController;
use App\Http\Controllers\Api\PunchCommentController;
use App\Http\Controllers\Api\PunchTemplateController;
use App\Http\Controllers\Api\PunchCategoryController;
use App\Http\Controllers\Api\PunchReportController;
use App\Http\Controllers\Api\PunchDashboardController;

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

    // Punch Lists
    Route::apiResource('punch-lists', PunchListController::class);
    Route::get('punch-lists/project/{projectId}', [PunchListController::class, 'byProject']);
    Route::post('punch-lists/{id}/issue', [PunchListController::class, 'issue']);
    Route::post('punch-lists/{id}/verify', [PunchListController::class, 'verify']);
    Route::post('punch-lists/{id}/close', [PunchListController::class, 'close']);
    Route::get('punch-lists/{id}/pdf', [PunchListController::class, 'generatePdf']);
    Route::post('punch-lists/{id}/send-notification', [PunchListController::class, 'sendNotification']);

    // Punch Items
    Route::apiResource('punch-items', PunchItemController::class);
    Route::get('punch-items/list/{listId}', [PunchItemController::class, 'byList']);
    Route::post('punch-items/{id}/assign', [PunchItemController::class, 'assign']);
    Route::post('punch-items/{id}/complete', [PunchItemController::class, 'complete']);
    Route::post('punch-items/{id}/verify', [PunchItemController::class, 'verify']);
    Route::post('punch-items/{id}/reject', [PunchItemController::class, 'reject']);
    Route::post('punch-items/{id}/reopen', [PunchItemController::class, 'reopen']);
    Route::post('punch-items/{id}/photos', [PunchItemController::class, 'uploadPhotos']);
    Route::post('punch-items/{id}/completion-photos', [PunchItemController::class, 'uploadCompletionPhotos']);
    Route::post('punch-items/bulk-update', [PunchItemController::class, 'bulkUpdate']);
    Route::post('punch-items/bulk-assign', [PunchItemController::class, 'bulkAssign']);
    Route::get('punch-items/{itemId}/history', [PunchItemController::class, 'history']);

    // Comments
    Route::get('punch-items/{itemId}/comments', [PunchCommentController::class, 'byItem']);
    Route::post('punch-items/{itemId}/comments', [PunchCommentController::class, 'store']);

    // Templates
    Route::apiResource('punch-templates', PunchTemplateController::class);
    Route::post('punch-lists/{listId}/apply-template/{templateId}', [PunchTemplateController::class, 'applyTemplate']);

    // Categories
    Route::apiResource('punch-categories', PunchCategoryController::class);
    Route::get('punch-categories/tree', [PunchCategoryController::class, 'tree']);

    // Dashboard
    Route::get('punch-dashboard/project/{projectId}', [PunchDashboardController::class, 'projectDashboard']);
    Route::get('punch-dashboard/summary/{projectId}', [PunchDashboardController::class, 'summary']);
    Route::get('punch-dashboard/by-discipline/{projectId}', [PunchDashboardController::class, 'byDiscipline']);
    Route::get('punch-dashboard/by-contractor/{projectId}', [PunchDashboardController::class, 'byContractor']);
    Route::get('punch-dashboard/by-location/{projectId}', [PunchDashboardController::class, 'byLocation']);
    Route::get('punch-dashboard/aging/{projectId}', [PunchDashboardController::class, 'aging']);
    Route::get('punch-dashboard/trend/{projectId}', [PunchDashboardController::class, 'trend']);

    // Reports
    Route::get('reports/punch-summary/{projectId}', [PunchReportController::class, 'summary']);
    Route::get('reports/punch-detailed/{projectId}', [PunchReportController::class, 'detailed']);
    Route::get('reports/punch-by-contractor/{projectId}', [PunchReportController::class, 'byContractor']);
    Route::get('reports/punch-overdue/{projectId}', [PunchReportController::class, 'overdue']);
    Route::get('reports/punch-statistics/{projectId}', [PunchReportController::class, 'statistics']);
    Route::post('reports/punch-export/{projectId}', [PunchReportController::class, 'export']);
});
