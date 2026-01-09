<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DailyReportController;

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
    
    // Daily Reports
    Route::resource('daily-reports', DailyReportController::class);
    Route::get('daily-reports/{dailyReport}/sign', [DailyReportController::class, 'sign'])->name('daily-reports.sign');
    Route::post('daily-reports/{dailyReport}/sign', [DailyReportController::class, 'signReport'])->name('daily-reports.sign.post');
    Route::get('daily-reports-photos', [DailyReportController::class, 'photos'])->name('daily-reports.photos');
    Route::get('daily-reports-weather', [DailyReportController::class, 'weatherLog'])->name('daily-reports.weather');
});
