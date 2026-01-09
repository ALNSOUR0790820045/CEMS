<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SubcontractorController;
use App\Http\Controllers\Api\SubcontractorAgreementController;
use App\Http\Controllers\Api\SubcontractorWorkOrderController;
use App\Http\Controllers\Api\SubcontractorIpcController;
use App\Http\Controllers\Api\SubcontractorEvaluationController;

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

Route::middleware(['auth:sanctum'])->group(function () {
    // Subcontractors
    Route::apiResource('subcontractors', SubcontractorController::class);
    Route::post('subcontractors/{id}/approve', [SubcontractorController::class, 'approve']);
    Route::post('subcontractors/{id}/blacklist', [SubcontractorController::class, 'blacklist']);
    
    // Subcontractor Agreements
    Route::apiResource('subcontractor-agreements', SubcontractorAgreementController::class);
    
    // Subcontractor Work Orders
    Route::apiResource('subcontractor-work-orders', SubcontractorWorkOrderController::class);
    
    // Subcontractor IPCs
    Route::apiResource('subcontractor-ipcs', SubcontractorIpcController::class);
    Route::post('subcontractor-ipcs/{id}/approve', [SubcontractorIpcController::class, 'approve']);
    Route::get('subcontractor-ipcs/{id}/pdf', [SubcontractorIpcController::class, 'pdf']);
    
    // Subcontractor Evaluations
    Route::apiResource('subcontractor-evaluations', SubcontractorEvaluationController::class);
});
