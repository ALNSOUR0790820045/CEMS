<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EmployeeDocumentController;
use App\Http\Controllers\Api\EmployeeDependentController;
use App\Http\Controllers\Api\EmployeeQualificationController;
use App\Http\Controllers\Api\EmployeeWorkHistoryController;
use App\Http\Controllers\Api\EmployeeSkillController;
use App\Http\Controllers\Api\PhotoAlbumController;
use App\Http\Controllers\Api\PhotoController;
use App\Http\Controllers\Api\PhotoAnnotationController;
use App\Http\Controllers\Api\PhotoComparisonController;
use App\Http\Controllers\Api\PhotoReportController;
use App\Http\Controllers\Api\PhotoTagController;
use App\Http\Controllers\Api\PhotoLocationController;

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

    // Photo Albums
    Route::apiResource('photo-albums', PhotoAlbumController::class);
    Route::get('photo-albums/project/{projectId}', [PhotoAlbumController::class, 'byProject']);
    Route::post('photo-albums/{id}/set-cover', [PhotoAlbumController::class, 'setCover']);

    // Photos
    Route::apiResource('photos', PhotoController::class);
    Route::get('photos/project/{projectId}', [PhotoController::class, 'byProject']);
    Route::get('photos/album/{albumId}', [PhotoController::class, 'byAlbum']);
    Route::post('photos/upload', [PhotoController::class, 'upload']);
    Route::post('photos/bulk-upload', [PhotoController::class, 'bulkUpload']);
    Route::post('photos/{id}/approve', [PhotoController::class, 'approve']);
    Route::post('photos/{id}/feature', [PhotoController::class, 'toggleFeatured']);
    Route::get('photos/search', [PhotoController::class, 'search']);
    Route::get('photos/by-location', [PhotoController::class, 'byLocation']);
    Route::get('photos/by-date-range', [PhotoController::class, 'byDateRange']);
    Route::get('photos/by-tag/{tag}', [PhotoController::class, 'byTag']);
    Route::post('photos/{id}/download', [PhotoController::class, 'download']);
    Route::post('photos/bulk-download', [PhotoController::class, 'bulkDownload']);

    // Annotations
    Route::get('photos/{photoId}/annotations', [PhotoAnnotationController::class, 'byPhoto']);
    Route::post('photos/{photoId}/annotations', [PhotoAnnotationController::class, 'store']);
    Route::put('photo-annotations/{id}', [PhotoAnnotationController::class, 'update']);
    Route::delete('photo-annotations/{id}', [PhotoAnnotationController::class, 'destroy']);

    // Comparisons
    Route::apiResource('photo-comparisons', PhotoComparisonController::class);
    Route::get('photo-comparisons/project/{projectId}', [PhotoComparisonController::class, 'byProject']);

    // Photo Reports
    Route::apiResource('photo-reports', PhotoReportController::class);
    Route::post('photo-reports/{id}/add-photos', [PhotoReportController::class, 'addPhotos']);
    Route::post('photo-reports/{id}/generate-pdf', [PhotoReportController::class, 'generatePdf']);
    Route::post('photo-reports/{id}/publish', [PhotoReportController::class, 'publish']);

    // Tags
    Route::apiResource('photo-tags', PhotoTagController::class);
    Route::get('photo-tags/popular', [PhotoTagController::class, 'popular']);

    // Locations
    Route::apiResource('photo-locations', PhotoLocationController::class);
    Route::get('photo-locations/project/{projectId}', [PhotoLocationController::class, 'byProject']);
});
