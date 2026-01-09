<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CostPlusContractController;
use App\Http\Controllers\CostPlusTransactionController;
use App\Http\Controllers\CostPlusInvoiceController;
use App\Http\Controllers\CostPlusOverheadController;
use App\Http\Controllers\CostPlusReportController;

// Guest Routes
Route::middleware('guest')->group(function () {
    Route:: get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login. post');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    
    // Companies Management
    Route::resource('companies', \App\Http\Controllers\CompanyController::class);
    
    // Main IPCs Management
    Route::get('main-ipcs-report', [\App\Http\Controllers\MainIpcController::class, 'report'])->name('main-ipcs.report');
    Route::get('main-ipcs/boq-items', [\App\Http\Controllers\MainIpcController::class, 'getBoqItems'])->name('main-ipcs.boq-items');
    Route::resource('main-ipcs', \App\Http\Controllers\MainIpcController::class);
    Route::post('main-ipcs/{mainIpc}/submit', [\App\Http\Controllers\MainIpcController::class, 'submitForApproval'])->name('main-ipcs.submit');
    Route::get('main-ipcs/{mainIpc}/approve', [\App\Http\Controllers\MainIpcController::class, 'approve'])->name('main-ipcs.approve');
    Route::post('main-ipcs/{mainIpc}/approve', [\App\Http\Controllers\MainIpcController::class, 'processApproval'])->name('main-ipcs.process-approval');
    Route::get('main-ipcs/{mainIpc}/payment', [\App\Http\Controllers\MainIpcController::class, 'payment'])->name('main-ipcs.payment');
    Route::post('main-ipcs/{mainIpc}/payment', [\App\Http\Controllers\MainIpcController::class, 'processPayment'])->name('main-ipcs.process-payment');
});
