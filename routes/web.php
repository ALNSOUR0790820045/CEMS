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
    
    // Cost Plus Management Module
    Route::prefix('cost-plus')->name('cost-plus.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [CostPlusReportController::class, 'dashboard'])->name('dashboard');
        
        // Contracts
        Route::resource('contracts', CostPlusContractController::class);
        
        // Transactions
        Route::resource('transactions', CostPlusTransactionController::class);
        Route::post('/transactions/{id}/approve', [CostPlusTransactionController::class, 'approve'])->name('transactions.approve');
        Route::post('/transactions/{id}/upload-documents', [CostPlusTransactionController::class, 'uploadDocuments'])->name('transactions.upload-documents');
        
        // Invoices
        Route::get('/invoices', [CostPlusInvoiceController::class, 'index'])->name('invoices.index');
        Route::post('/invoices/generate', [CostPlusInvoiceController::class, 'generate'])->name('invoices.generate');
        Route::get('/invoices/{id}', [CostPlusInvoiceController::class, 'show'])->name('invoices.show');
        Route::post('/invoices/{id}/approve', [CostPlusInvoiceController::class, 'approve'])->name('invoices.approve');
        Route::get('/invoices/{id}/export', [CostPlusInvoiceController::class, 'export'])->name('invoices.export');
        
        // Overhead
        Route::get('/overhead', [CostPlusOverheadController::class, 'index'])->name('overhead.index');
        Route::post('/overhead/allocate', [CostPlusOverheadController::class, 'allocate'])->name('overhead.allocate');
        
        // Reports
        Route::get('/gmp-status', [CostPlusReportController::class, 'gmpStatus'])->name('gmp-status');
        Route::get('/open-book-report', [CostPlusReportController::class, 'openBookReport'])->name('open-book-report');
        Route::get('/reports', [CostPlusReportController::class, 'reports'])->name('reports');
    });
});
