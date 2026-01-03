<?php

use App\Http\Controllers\Api\ARInvoiceController;
use App\Http\Controllers\Api\ARReceiptController;
use App\Http\Controllers\Api\ARReportController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    // AR Invoices
    Route::apiResource('ar-invoices', ARInvoiceController::class);
    Route::post('ar-invoices/{id}/send', [ARInvoiceController::class, 'send']);

    // AR Receipts
    Route::apiResource('ar-receipts', ARReceiptController::class);
    Route::post('ar-receipts/{id}/allocate', [ARReceiptController::class, 'allocate']);

    // AR Reports
    Route::get('ar-reports/aging', [ARReportController::class, 'aging']);
    Route::get('ar-reports/client-balance', [ARReportController::class, 'clientBalance']);
    Route::get('ar-reports/collection-forecast', [ARReportController::class, 'collectionForecast']);
    Route::get('ar-reports/dso', [ARReportController::class, 'dso']);
});
