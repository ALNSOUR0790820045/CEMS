<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SiteReceiptController;

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    
    // Companies Management
    Route::resource('companies', \App\Http\Controllers\CompanyController::class);
    
    // Site Receipts Management
    Route::resource('site-receipts', SiteReceiptController::class);
    Route::get('site-receipts/{siteReceipt}/verify', [SiteReceiptController::class, 'verify'])->name('site-receipts.verify');
    Route::post('site-receipts/{siteReceipt}/verify', [SiteReceiptController::class, 'processVerification'])->name('site-receipts.process-verification');
    Route::get('purchase-orders/{purchaseOrder}/items', [SiteReceiptController::class, 'getPOItems'])->name('purchase-orders.items');
});