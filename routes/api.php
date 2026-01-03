<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApInvoiceController;
use App\Http\Controllers\Api\ApPaymentController;
use App\Http\Controllers\Api\ApReportController;

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
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // AP Invoices
    Route::prefix('ap-invoices')->group(function () {
        Route::get('/', [ApInvoiceController::class, 'index']);
        Route::post('/', [ApInvoiceController::class, 'store']);
        Route::get('/{invoice}', [ApInvoiceController::class, 'show']);
        Route::put('/{invoice}', [ApInvoiceController::class, 'update']);
        Route::delete('/{invoice}', [ApInvoiceController::class, 'destroy']);
        Route::post('/{invoice}/approve', [ApInvoiceController::class, 'approve']);
    });

    // AP Payments
    Route::prefix('ap-payments')->group(function () {
        Route::get('/', [ApPaymentController::class, 'index']);
        Route::post('/', [ApPaymentController::class, 'store']);
        Route::get('/{payment}', [ApPaymentController::class, 'show']);
        Route::put('/{payment}', [ApPaymentController::class, 'update']);
        Route::delete('/{payment}', [ApPaymentController::class, 'destroy']);
        Route::post('/{payment}/allocate', [ApPaymentController::class, 'allocate']);
    });

    // AP Reports
    Route::prefix('ap-reports')->group(function () {
        Route::get('/aging', [ApReportController::class, 'aging']);
        Route::get('/vendor-balance', [ApReportController::class, 'vendorBalance']);
        Route::get('/payment-history', [ApReportController::class, 'paymentHistory']);
        Route::get('/cash-flow-forecast', [ApReportController::class, 'cashFlowForecast']);
    });
});
