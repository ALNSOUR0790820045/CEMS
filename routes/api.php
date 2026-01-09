<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UnforeseeableConditionController;

Route::middleware('auth:sanctum')->group(function () {
    // Unforeseeable Conditions API
    Route::apiResource('unforeseeable-conditions', UnforeseeableConditionController::class);
    Route::post('unforeseeable-conditions/{id}/send-notice', [UnforeseeableConditionController::class, 'sendNotice']);
    Route::post('unforeseeable-conditions/{id}/evidence', [UnforeseeableConditionController::class, 'uploadEvidence']);
    Route::get('unforeseeable-conditions/{id}/export', [UnforeseeableConditionController::class, 'export']);
});
