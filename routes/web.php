<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportsDashboardController;

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
    
    // Financial Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportsDashboardController::class, 'index'])->name('index');
        Route::get('/history', [ReportsDashboardController::class, 'history'])->name('history');
    });
});