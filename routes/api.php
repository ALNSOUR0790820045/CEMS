<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VariationOrderController;

Route::middleware('auth:sanctum')->group(function () {
    // Variation Orders API
    Route::get('/variation-orders', [VariationOrderController::class, 'index']);
    Route::post('/variation-orders', [VariationOrderController::class, 'store']);
    Route::get('/variation-orders/{variationOrder}', [VariationOrderController::class, 'show']);
    Route::put('/variation-orders/{variationOrder}', [VariationOrderController::class, 'update']);
    Route::delete('/variation-orders/{variationOrder}', [VariationOrderController::class, 'destroy']);
    
    // Workflow actions
    Route::post('/variation-orders/{variationOrder}/submit', [VariationOrderController::class, 'submit']);
    Route::post('/variation-orders/{variationOrder}/approve', [VariationOrderController::class, 'approve']);
    Route::post('/variation-orders/{variationOrder}/reject', [VariationOrderController::class, 'reject']);
    
    // Additional endpoints
    Route::get('/variation-orders/statistics', [VariationOrderController::class, 'statistics']);
    Route::get('/variation-orders/{variationOrder}/export', [VariationOrderController::class, 'export']);
    Route::get('/projects/{project}/variation-orders', [VariationOrderController::class, 'byProject']);
});
