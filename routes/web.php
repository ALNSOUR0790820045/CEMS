<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ContractController;

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
    
    // Contracts Management
    Route::resource('contracts', ContractController::class);
    Route::post('contracts/{contract}/clone', [ContractController::class, 'clone'])->name('contracts.clone');
    Route::get('contracts/generate-code', [ContractController::class, 'generateCode'])->name('contracts.generate-code');
});