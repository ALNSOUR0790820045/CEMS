<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FixedAssetController;
use App\Http\Controllers\Api\AssetReportController;

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

// Fixed Assets API Routes
Route::prefix('fixed-assets')->group(function () {
    Route::get('/', [FixedAssetController::class, 'index']);
    Route::post('/', [FixedAssetController::class, 'store']);
    Route::get('/{fixedAsset}', [FixedAssetController::class, 'show']);
    Route::put('/{fixedAsset}', [FixedAssetController::class, 'update']);
    Route::delete('/{fixedAsset}', [FixedAssetController::class, 'destroy']);
    
    // Special actions
    Route::post('/calculate-depreciation', [FixedAssetController::class, 'calculateDepreciation']);
    Route::post('/{fixedAsset}/dispose', [FixedAssetController::class, 'dispose']);
});

// Asset Reports API Routes
Route::prefix('reports')->group(function () {
    Route::get('/asset-register', [AssetReportController::class, 'assetRegister']);
    Route::get('/depreciation-schedule', [AssetReportController::class, 'depreciationSchedule']);
    Route::get('/asset-valuation', [AssetReportController::class, 'assetValuation']);
});
