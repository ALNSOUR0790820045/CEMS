<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VendorController;

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
    
    // Vendors Management
    Route::resource('vendors', VendorController::class);
    Route::post('vendors/{vendor}/approve', [VendorController::class, 'approve'])->name('vendors.approve');
    Route::post('vendors/{vendor}/reject', [VendorController::class, 'reject'])->name('vendors.reject');
    Route::get('api/vendors/generate-code', [VendorController::class, 'generateCode'])->name('vendors.generate-code');
});
