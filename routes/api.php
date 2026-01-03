<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\InventoryController;
use App\Http\Controllers\Api\StockTransferController;
use App\Http\Controllers\Api\InventoryReportController;

Route::middleware(['auth:sanctum'])->group(function () {
    // Inventory Management
    Route::get('/inventory/balance', [InventoryController::class, 'getBalance']);
    Route::get('/inventory/transactions', [InventoryController::class, 'getTransactions']);
    Route::post('/inventory/transactions', [InventoryController::class, 'createTransaction']);

    // Stock Transfers
    Route::get('/stock-transfers', [StockTransferController::class, 'index']);
    Route::post('/stock-transfers', [StockTransferController::class, 'store']);
    Route::get('/stock-transfers/{id}', [StockTransferController::class, 'show']);
    Route::post('/stock-transfers/{id}/approve', [StockTransferController::class, 'approve']);
    Route::post('/stock-transfers/{id}/receive', [StockTransferController::class, 'receive']);
    Route::post('/stock-transfers/{id}/cancel', [StockTransferController::class, 'cancel']);

    // Inventory Reports
    Route::get('/inventory/reports/valuation', [InventoryReportController::class, 'valuation']);
    Route::get('/inventory/reports/stock-status', [InventoryReportController::class, 'stockStatus']);
    Route::get('/inventory/reports/movement', [InventoryReportController::class, 'movement']);
    Route::get('/inventory/reports/low-stock', [InventoryReportController::class, 'lowStock']);
});
