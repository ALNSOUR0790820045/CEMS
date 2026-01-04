<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PurchaseRequisitionController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Purchase Requisition API Routes
Route::prefix('purchase-requisitions')->group(function () {
    Route::get('/', [PurchaseRequisitionController::class, 'index']);
    Route::post('/', [PurchaseRequisitionController::class, 'store']);
    Route::get('/{id}', [PurchaseRequisitionController::class, 'show']);
    Route::put('/{id}', [PurchaseRequisitionController::class, 'update']);
    Route::delete('/{id}', [PurchaseRequisitionController::class, 'destroy']);
    Route::post('/{id}/approve', [PurchaseRequisitionController::class, 'approve']);
    Route::post('/{id}/reject', [PurchaseRequisitionController::class, 'reject']);
    Route::post('/{id}/convert-to-po', [PurchaseRequisitionController::class, 'convertToPO']);
});

