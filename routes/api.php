<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CostCenterController;
use App\Http\Controllers\Api\BudgetController;
use App\Http\Controllers\Api\CostAllocationController;
use App\Http\Controllers\Api\ReportController;

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
    // Cost Centers
    Route::apiResource('cost-centers', CostCenterController::class);
    
    // Budgets
    Route::apiResource('budgets', BudgetController::class);
    
    // Cost Allocations
    Route::apiResource('cost-allocations', CostAllocationController::class);
    
    // Reports
    Route::prefix('reports')->group(function () {
        Route::get('cost-analysis', [ReportController::class, 'costAnalysis']);
        Route::get('budget-variance', [ReportController::class, 'budgetVariance']);
        Route::get('cost-center-report', [ReportController::class, 'costCenterReport']);
    });
});
