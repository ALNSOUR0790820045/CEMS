<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApInvoiceController;
use App\Http\Controllers\Api\ApPaymentController;
use App\Http\Controllers\Api\ApReportController;
use App\Http\Controllers\Api\ARInvoiceController;
use App\Http\Controllers\Api\ARReceiptController;
use App\Http\Controllers\Api\ARReportController;
// ... (باقي الـ imports من main)

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum'])->group(function () {
    
    // AP Invoices
    Route::prefix('ap-invoices')->group(function () {
        Route::get('/', [ApInvoiceController::class, 'index']);
        Route::post('/', [ApInvoiceController::class, 'store']);
        Route::get('/{invoice}', [ApInvoiceController::class, 'show']);
        Route::put('/{invoice}', [ApInvoiceController:: class, 'update']);
        Route::delete('/{invoice}', [ApInvoiceController::class, 'destroy']);
        Route::post('/{invoice}/approve', [ApInvoiceController:: class, 'approve']);
    });

    // AP Payments
    Route::prefix('ap-payments')->group(function () {
        Route::get('/', [ApPaymentController::class, 'index']);
        Route::post('/', [ApPaymentController::class, 'store']);
        Route::get('/{payment}', [ApPaymentController::class, 'show']);
        Route::put('/{payment}', [ApPaymentController::class, 'update']);
        Route::delete('/{payment}', [ApPaymentController:: class, 'destroy']);
        Route::post('/{payment}/allocate', [ApPaymentController:: class, 'allocate']);
    });

    // AP Reports
    Route::prefix('ap-reports')->group(function () {
        Route::get('/aging', [ApReportController:: class, 'aging']);
        Route::get('/vendor-balance', [ApReportController::class, 'vendorBalance']);
        Route::get('/payment-history', [ApReportController::class, 'paymentHistory']);
        Route::get('/cash-flow-forecast', [ApReportController:: class, 'cashFlowForecast']);
    });

    // AR Invoices
    Route::apiResource('ar-invoices', ARInvoiceController::class);
    Route::post('ar-invoices/{id}/send', [ARInvoiceController:: class, 'send']);

    // AR Receipts
    Route::apiResource('ar-receipts', ARReceiptController::class);
    Route::post('ar-receipts/{id}/allocate', [ARReceiptController:: class, 'allocate']);

    // AR Reports
    Route:: get('ar-reports/aging', [ARReportController::class, 'aging']);
    Route::get('ar-reports/client-balance', [ARReportController::class, 'clientBalance']);
    Route::get('ar-reports/collection-forecast', [ARReportController::class, 'collectionForecast']);
    Route::get('ar-reports/dso', [ARReportController::class, 'dso']);
    
    // ...  (باقي الـ routes من main - انسخها كلها)
});