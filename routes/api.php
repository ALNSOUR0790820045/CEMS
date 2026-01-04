<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RegretIndexController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Regret Index API Routes
Route::middleware('auth:sanctum')->prefix('regret-index')->group(function () {
    Route::get('/', [RegretIndexController::class, 'index']);
    Route::post('/calculate', [RegretIndexController::class, 'calculate']);
    Route::get('/{id}', [RegretIndexController::class, 'show']);
    Route::post('/{id}/scenarios', [RegretIndexController::class, 'addScenario']);
    Route::get('/{id}/export', [RegretIndexController::class, 'export']);
    Route::get('/{id}/presentation', [RegretIndexController::class, 'presentation']);
});
