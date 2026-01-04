<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PurchaseOrderController;

Route::middleware(['auth:sanctum'])->group(function () {
    // Purchase Orders
    Route::apiResource('purchase-orders', PurchaseOrderController::class);
    
    // Purchase Order Actions
    Route::post('purchase-orders/{purchaseOrder}/approve', [PurchaseOrderController::class, 'approve']);
    Route::post('purchase-orders/{purchaseOrder}/send-to-vendor', [PurchaseOrderController::class, 'sendToVendor']);
    Route::post('purchase-orders/{purchaseOrder}/amend', [PurchaseOrderController::class, 'amend']);
    Route::get('purchase-orders/{purchaseOrder}/receiving-status', [PurchaseOrderController::class, 'receivingStatus']);
});
