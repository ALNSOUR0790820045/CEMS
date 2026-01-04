<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BankAccountController;
use App\Http\Controllers\Api\BankStatementController;
use App\Http\Controllers\Api\BankReconciliationReportController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application.
| These routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/

// Bank Accounts
Route::prefix('bank-accounts')->group(function () {
    Route::get('/', [BankAccountController::class, 'index']);
    Route::post('/', [BankAccountController::class, 'store']);
    Route::get('/{id}', [BankAccountController::class, 'show']);
    Route::put('/{id}', [BankAccountController::class, 'update']);
    Route::delete('/{id}', [BankAccountController::class, 'destroy']);
});

// Bank Statements
Route::prefix('bank-statements')->group(function () {
    Route::get('/', [BankStatementController::class, 'index']);
    Route::post('/', [BankStatementController::class, 'store']);
    Route::get('/{id}', [BankStatementController::class, 'show']);
    Route::post('/import', [BankStatementController::class, 'import']);
    Route::post('/{id}/reconcile', [BankStatementController::class, 'reconcile']);
});

// Bank Reconciliation Reports
Route::prefix('bank-reconciliation-report')->group(function () {
    Route::get('/', [BankReconciliationReportController::class, 'index']);
    Route::get('/outstanding-items', [BankReconciliationReportController::class, 'outstandingItems']);
    Route::get('/bank-book', [BankReconciliationReportController::class, 'bankBook']);
});
