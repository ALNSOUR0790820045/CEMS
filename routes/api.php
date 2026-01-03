<?php

use App\Http\Controllers\Api\MaterialRequestController;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->group(function () {
    // Material Request Routes
    Route::apiResource('material-requests', MaterialRequestController::class);
    
    // Material Request Actions
    Route::post('material-requests/{materialRequest}/approve', [MaterialRequestController::class, 'approve']);
    Route::post('material-requests/{materialRequest}/issue', [MaterialRequestController::class, 'issue']);
    Route::post('material-requests/{materialRequest}/convert-to-pr', [MaterialRequestController::class, 'convertToPurchaseRequisition']);
});
