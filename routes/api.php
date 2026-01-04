<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
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

// Warehouse Management API Routes
Route::middleware('auth:sanctum')->group(function () {
    // Warehouses
    Route::apiResource('warehouses', WarehouseController::class);
    
    // Warehouse Locations
    Route::apiResource('warehouse-locations', WarehouseLocationController::class);
    
    // Warehouse Stock
    Route::get('warehouse-stock', [WarehouseStockController::class, 'index']);
    Route::get('warehouse-stock/availability', [WarehouseStockController::class, 'availability']);
    Route::post('warehouse-stock/transfer', [WarehouseStockController::class, 'transfer']);
});
