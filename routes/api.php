<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PettyCashAccountController;
use App\Http\Controllers\Api\PettyCashTransactionController;
use App\Http\Controllers\Api\PettyCashReimburseController;

Route::middleware('auth:sanctum')->group(function () {
    // Petty Cash Accounts
    Route::get('petty-cash-accounts', [PettyCashAccountController::class, 'index']);
    Route::post('petty-cash-accounts', [PettyCashAccountController::class, 'store']);
    Route::get('petty-cash-accounts/{pettyCashAccount}', [PettyCashAccountController::class, 'show']);
    Route::put('petty-cash-accounts/{pettyCashAccount}', [PettyCashAccountController::class, 'update']);
    Route::delete('petty-cash-accounts/{pettyCashAccount}', [PettyCashAccountController::class, 'destroy']);

    // Petty Cash Transactions
    Route::get('petty-cash-transactions', [PettyCashTransactionController::class, 'index']);
    Route::post('petty-cash-transactions', [PettyCashTransactionController::class, 'store']);
    Route::get('petty-cash-transactions/{pettyCashTransaction}', [PettyCashTransactionController::class, 'show']);
    Route::put('petty-cash-transactions/{pettyCashTransaction}', [PettyCashTransactionController::class, 'update']);
    Route::delete('petty-cash-transactions/{pettyCashTransaction}', [PettyCashTransactionController::class, 'destroy']);

    // Petty Cash Reimbursement
    Route::post('petty-cash/reimburse', [PettyCashReimburseController::class, 'reimburse']);
});
