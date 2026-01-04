<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\NotificationPreferenceController;
use App\Http\Controllers\Api\AlertRuleController;

Route::middleware(['auth:sanctum'])->group(function () {
    // Notification endpoints
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{notification}/mark-read', [NotificationController::class, 'markRead']);
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead']);
    
    // Notification preferences endpoints
    Route::get('/notification-preferences', [NotificationPreferenceController::class, 'index']);
    Route::put('/notification-preferences', [NotificationPreferenceController::class, 'update']);
    
    // Alert rules endpoints
    Route::get('/alert-rules', [AlertRuleController::class, 'index']);
    Route::post('/alert-rules', [AlertRuleController::class, 'store']);
    Route::get('/alert-rules/{alertRule}', [AlertRuleController::class, 'show']);
    Route::put('/alert-rules/{alertRule}', [AlertRuleController::class, 'update']);
    Route::delete('/alert-rules/{alertRule}', [AlertRuleController::class, 'destroy']);
});
