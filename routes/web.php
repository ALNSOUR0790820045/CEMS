<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;

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
    
    // Variation Orders Management
    Route::resource('variation-orders', \App\Http\Controllers\VariationOrderController::class);
    Route::post('variation-orders/{variationOrder}/submit', [\App\Http\Controllers\VariationOrderController::class, 'submit'])->name('variation-orders.submit');
    Route::post('variation-orders/{variationOrder}/approve', [\App\Http\Controllers\VariationOrderController::class, 'approve'])->name('variation-orders.approve');
    Route::post('variation-orders/{variationOrder}/reject', [\App\Http\Controllers\VariationOrderController::class, 'reject'])->name('variation-orders.reject');
    Route::get('variation-orders/{variationOrder}/export', [\App\Http\Controllers\VariationOrderController::class, 'export'])->name('variation-orders.export');
});