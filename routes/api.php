<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\MaterialCategoryController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Materials API Routes
// Note: Add ->middleware('auth:sanctum') to protect these routes in production
Route::prefix('materials')->group(function () {
    Route::get('/', [MaterialController::class, 'index']);
    Route::post('/', [MaterialController::class, 'store']);
    Route::get('/{id}', [MaterialController::class, 'show']);
    Route::put('/{id}', [MaterialController::class, 'update']);
    Route::delete('/{id}', [MaterialController::class, 'destroy']);
    
    // Material vendors
    Route::get('/{id}/vendors', [MaterialController::class, 'vendors']);
    Route::post('/{id}/vendors', [MaterialController::class, 'addVendor']);
});

// Material Categories API Routes
// Note: Add ->middleware('auth:sanctum') to protect these routes in production
Route::prefix('material-categories')->group(function () {
    Route::get('/', [MaterialCategoryController::class, 'index']);
    Route::post('/', [MaterialCategoryController::class, 'store']);
    Route::get('/{id}', [MaterialCategoryController::class, 'show']);
    Route::put('/{id}', [MaterialCategoryController::class, 'update']);
    Route::delete('/{id}', [MaterialCategoryController::class, 'destroy']);
});
