<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EotClaimController;

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
    
    // EOT Claims Management
    Route::get('/eot/dashboard', [EotClaimController::class, 'dashboard'])->name('eot.dashboard');
    Route::get('/eot/report', [EotClaimController::class, 'report'])->name('eot.report');
    Route::post('/eot/{eotClaim}/submit', [EotClaimController::class, 'submit'])->name('eot.submit');
    Route::get('/eot/{eotClaim}/approve', [EotClaimController::class, 'approvalForm'])->name('eot.approval-form');
    Route::post('/eot/{eotClaim}/approve', [EotClaimController::class, 'approve'])->name('eot.approve');
    Route::resource('eot', EotClaimController::class);
});
