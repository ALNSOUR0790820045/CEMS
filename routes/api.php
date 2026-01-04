<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\KpiController;
use App\Http\Controllers\Api\ChartController;

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
    // Dashboard Endpoints
    Route::get('/dashboard/executive', [DashboardController::class, 'executive']);
    Route::get('/dashboard/project/{id}', [DashboardController::class, 'project']);
    Route::get('/dashboard/financial', [DashboardController::class, 'financial']);
    Route::post('/dashboard/save-layout', [DashboardController::class, 'saveLayout']);
    
    // KPI Endpoints
    Route::get('/kpis', [KpiController::class, 'index']);
    
    // Chart Endpoints
    Route::get('/charts/{chart_type}', [ChartController::class, 'show']);
    
    // Projects List Endpoint
    Route::get('/projects', function () {
        return response()->json([
            'success' => true,
            'data' => \App\Models\Project::select('id', 'name', 'code', 'status', 'progress')
                ->orderBy('name')
                ->get(),
        ]);
    });
});
