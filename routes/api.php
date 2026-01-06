<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\WarehouseController;
use App\Http\Controllers\Api\WarehouseLocationController;
use App\Http\Controllers\Api\WarehouseStockController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum'])->group(function () {
    // Documents Management API
    Route::prefix('documents')->group(function () {
        Route::get('/', [DocumentController::class, 'index']);
        Route::post('/', [DocumentController::class, 'store']);
        Route::get('/search', [DocumentController::class, 'search']);
        Route::get('/{document}', [DocumentController::class, 'show']);
        Route::put('/{document}', [DocumentController::class, 'update']);
        Route::patch('/{document}', [DocumentController::class, 'update']);
        Route::delete('/{document}', [DocumentController::class, 'destroy']);
        Route::post('/{document}/upload-version', [DocumentController::class, 'uploadVersion']);
        Route::get('/{document}/versions', [DocumentController::class, 'versions']);
        Route::post('/{document}/grant-access', [DocumentController::class, 'grantAccess']);
    });
    
    // Warehouse Management API Routes
    // Warehouses
    Route::apiResource('warehouses', WarehouseController::class);
    
    // Warehouse Locations
    Route::apiResource('warehouse-locations', WarehouseLocationController:: class);
    
    // Warehouse Stock
    Route::get('warehouse-stock', [WarehouseStockController::class, 'index']);
    Route::get('warehouse-stock/availability', [WarehouseStockController:: class, 'availability']);
    Route::post('warehouse-stock/transfer', [WarehouseStockController::class, 'transfer']);
});