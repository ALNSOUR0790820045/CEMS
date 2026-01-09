<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EmployeeDocumentController;
use App\Http\Controllers\Api\EmployeeDependentController;
use App\Http\Controllers\Api\EmployeeQualificationController;
use App\Http\Controllers\Api\EmployeeWorkHistoryController;
use App\Http\Controllers\Api\EmployeeSkillController;
use App\Http\Controllers\Api\SiteDiaryController;
use App\Http\Controllers\Api\DiaryEntryController;
use App\Http\Controllers\Api\DiaryReportController;

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

    // Site Diaries
    Route::apiResource('site-diaries', SiteDiaryController::class);
    Route::get('site-diaries/by-date/{projectId}/{date}', [SiteDiaryController::class, 'byDate']);
    Route::get('site-diaries/latest/{projectId}', [SiteDiaryController::class, 'latest']);
    Route::post('site-diaries/{id}/submit', [SiteDiaryController::class, 'submit']);
    Route::post('site-diaries/{id}/review', [SiteDiaryController::class, 'review']);
    Route::post('site-diaries/{id}/approve', [SiteDiaryController::class, 'approve']);
    Route::post('site-diaries/{id}/reject', [SiteDiaryController::class, 'reject']);
    Route::post('site-diaries/{id}/duplicate', [SiteDiaryController::class, 'duplicateFromPrevious']);

    // Diary Entries
    Route::post('site-diaries/{id}/manpower', [DiaryEntryController::class, 'addManpower']);
    Route::put('site-diaries/{id}/manpower/{entryId}', [DiaryEntryController::class, 'updateManpower']);
    Route::delete('site-diaries/{id}/manpower/{entryId}', [DiaryEntryController::class, 'deleteManpower']);
    Route::post('site-diaries/{id}/equipment', [DiaryEntryController::class, 'addEquipment']);
    Route::post('site-diaries/{id}/activities', [DiaryEntryController::class, 'addActivity']);
    Route::post('site-diaries/{id}/materials', [DiaryEntryController::class, 'addMaterial']);
    Route::post('site-diaries/{id}/visitors', [DiaryEntryController::class, 'addVisitor']);
    Route::post('site-diaries/{id}/incidents', [DiaryEntryController::class, 'addIncident']);
    Route::post('site-diaries/{id}/instructions', [DiaryEntryController::class, 'addInstruction']);
    Route::post('site-diaries/{id}/photos', [DiaryEntryController::class, 'uploadPhoto']);

    // Reports
    Route::get('reports/daily-summary/{projectId}', [DiaryReportController::class, 'dailySummary']);
    Route::get('reports/weekly-summary/{projectId}', [DiaryReportController::class, 'weeklySummary']);
    Route::get('reports/monthly-summary/{projectId}', [DiaryReportController::class, 'monthlySummary']);
    Route::get('reports/manpower-histogram/{projectId}', [DiaryReportController::class, 'manpowerHistogram']);
    Route::get('reports/equipment-utilization/{projectId}', [DiaryReportController::class, 'equipmentUtilization']);
    Route::get('reports/weather-analysis/{projectId}', [DiaryReportController::class, 'weatherAnalysis']);
    Route::get('reports/incident-log/{projectId}', [DiaryReportController::class, 'incidentLog']);
    Route::get('reports/progress-photos/{projectId}', [DiaryReportController::class, 'progressPhotos']);
});
