<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EmployeeDocumentController;
use App\Http\Controllers\Api\EmployeeDependentController;
use App\Http\Controllers\Api\EmployeeQualificationController;
use App\Http\Controllers\Api\EmployeeWorkHistoryController;
use App\Http\Controllers\Api\EmployeeSkillController;
use App\Http\Controllers\ProjectScheduleController;
use App\Http\Controllers\ScheduleActivityController;
use App\Http\Controllers\DependencyController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\BaselineController;
use App\Http\Controllers\ScheduleReportController;

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

    // Project Schedules
    Route::apiResource('project-schedules', ProjectScheduleController::class);
    Route::get('project-schedules/project/{projectId}', [ProjectScheduleController::class, 'byProject']);
    Route::post('project-schedules/{id}/approve', [ProjectScheduleController::class, 'approve']);
    Route::post('project-schedules/{id}/calculate', [ProjectScheduleController::class, 'calculate']);
    Route::post('project-schedules/{id}/set-baseline', [ProjectScheduleController::class, 'setBaseline']);
    Route::get('project-schedules/{id}/gantt', [ProjectScheduleController::class, 'ganttData']);
    Route::get('project-schedules/{id}/critical-path', [ProjectScheduleController::class, 'criticalPath']);

    // Schedule Activities
    Route::apiResource('schedule-activities', ScheduleActivityController::class);
    Route::get('schedule-activities/schedule/{scheduleId}', [ScheduleActivityController::class, 'bySchedule']);
    Route::post('schedule-activities/{id}/update-progress', [ScheduleActivityController::class, 'updateProgress']);
    Route::post('schedule-activities/bulk-update', [ScheduleActivityController::class, 'bulkUpdate']);
    Route::post('schedule-activities/import', [ScheduleActivityController::class, 'import']);

    // Dependencies
    Route::get('schedule-activities/{activityId}/predecessors', [DependencyController::class, 'predecessors']);
    Route::get('schedule-activities/{activityId}/successors', [DependencyController::class, 'successors']);
    Route::post('schedule-activities/{activityId}/dependencies', [DependencyController::class, 'addDependency']);
    Route::delete('dependencies/{id}', [DependencyController::class, 'removeDependency']);

    // Calendars
    Route::apiResource('schedule-calendars', CalendarController::class);
    Route::post('schedule-calendars/{id}/exceptions', [CalendarController::class, 'addException']);

    // Baselines
    Route::get('project-schedules/{scheduleId}/baselines', [BaselineController::class, 'bySchedule']);
    Route::post('project-schedules/{scheduleId}/baselines', [BaselineController::class, 'create']);
    Route::get('project-schedules/{scheduleId}/baseline-comparison', [BaselineController::class, 'compare']);

    // Reports
    Route::get('reports/schedule-summary/{projectId}', [ScheduleReportController::class, 'summary']);
    Route::get('reports/critical-activities/{projectId}', [ScheduleReportController::class, 'criticalActivities']);
    Route::get('reports/schedule-variance/{projectId}', [ScheduleReportController::class, 'variance']);
    Route::get('reports/look-ahead/{projectId}', [ScheduleReportController::class, 'lookAhead']);
    Route::get('reports/milestone-status/{projectId}', [ScheduleReportController::class, 'milestoneStatus']);
    Route::get('reports/resource-histogram/{projectId}', [ScheduleReportController::class, 'resourceHistogram']);
    Route::get('reports/s-curve/{projectId}', [ScheduleReportController::class, 'sCurve']);
});
