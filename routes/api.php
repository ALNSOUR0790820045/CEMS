<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CertificationController;
use App\Http\Controllers\ComplianceRequirementController;
use App\Http\Controllers\ComplianceTrackingController;
use App\Http\Controllers\ComplianceReportController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Certifications API Routes
Route::prefix('certifications')->group(function () {
    Route::get('/', [CertificationController::class, 'index']);
    Route::post('/', [CertificationController::class, 'store']);
    Route::get('/expiring', [CertificationController::class, 'expiring']);
    Route::get('/{certification}', [CertificationController::class, 'show']);
    Route::put('/{certification}', [CertificationController::class, 'update']);
    Route::patch('/{certification}', [CertificationController::class, 'update']);
    Route::delete('/{certification}', [CertificationController::class, 'destroy']);
    Route::post('/{certification}/renew', [CertificationController::class, 'renew']);
});

// Compliance Requirements API Routes
Route::prefix('compliance-requirements')->group(function () {
    Route::get('/', [ComplianceRequirementController::class, 'index']);
    Route::post('/', [ComplianceRequirementController::class, 'store']);
    Route::get('/{complianceRequirement}', [ComplianceRequirementController::class, 'show']);
    Route::put('/{complianceRequirement}', [ComplianceRequirementController::class, 'update']);
    Route::patch('/{complianceRequirement}', [ComplianceRequirementController::class, 'update']);
    Route::delete('/{complianceRequirement}', [ComplianceRequirementController::class, 'destroy']);
});

// Compliance Tracking API Routes
Route::prefix('compliance-tracking')->group(function () {
    Route::get('/', [ComplianceTrackingController::class, 'index']);
    Route::post('/', [ComplianceTrackingController::class, 'store']);
    Route::get('/overdue', [ComplianceTrackingController::class, 'overdue']);
    Route::get('/{complianceTracking}', [ComplianceTrackingController::class, 'show']);
    Route::put('/{complianceTracking}', [ComplianceTrackingController::class, 'update']);
    Route::patch('/{complianceTracking}', [ComplianceTrackingController::class, 'update']);
    Route::delete('/{complianceTracking}', [ComplianceTrackingController::class, 'destroy']);
});

// Reports API Routes
Route::prefix('reports')->group(function () {
    Route::get('/compliance-dashboard', [ComplianceReportController::class, 'dashboard']);
    Route::get('/certification-register', [ComplianceReportController::class, 'certificationRegister']);
    Route::get('/compliance-status', [ComplianceReportController::class, 'complianceStatus']);
});
