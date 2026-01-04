<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CashAccountController;
use App\Http\Controllers\Api\CashTransactionController;
use App\Http\Controllers\Api\CashFlowController;

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

// Cash Management API Routes
Route::middleware(['auth:sanctum'])->group(function () {
    
    // Cash Accounts
    Route::apiResource('cash-accounts', CashAccountController::class);
    
    // Cash Transactions
    Route::apiResource('cash-transactions', CashTransactionController::class);
    
    // Specific transaction endpoints
    Route::post('cash-transactions/receipt', [CashTransactionController::class, 'receipt']);
    Route::post('cash-transactions/payment', [CashTransactionController::class, 'payment']);
    Route::post('cash-transactions/transfer', [CashTransactionController::class, 'transfer']);
    
    // Cash Flow
    Route::get('cash-flow-forecast', [CashFlowController::class, 'forecast']);
    Route::get('cash-flow-summary', [CashFlowController::class, 'summary']);
});
