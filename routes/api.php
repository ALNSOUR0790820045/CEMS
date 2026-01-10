<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TimeBarController;
use App\Http\Controllers\Api\EmployeeDocumentController;
use App\Http\Controllers\Api\EmployeeDependentController;

Route::prefix('v1')->group(function () {
    // Material Categories
    Route::prefix('material-categories')->group(function () {
        Route::get('/', [MaterialCategoryController::class, 'index']);
        Route::post('/', [MaterialCategoryController:: class, 'store']);
        Route::get('/{id}', [MaterialCategoryController::class, 'show']);
        Route::put('/{id}', [MaterialCategoryController::class, 'update']);
        Route::delete('/{id}', [MaterialCategoryController::class, 'destroy']);
    });

    // Clauses
    Route::get('/clauses', [TimeBarController::class, 'clauses'])->name('clauses');

    // Reports
    Route::get('reports/incident-log/{projectId}', [DiaryReportController::class, 'incidentLog']);
    Route::get('reports/progress-photos/{projectId}', [DiaryReportController:: class, 'progressPhotos']);
});