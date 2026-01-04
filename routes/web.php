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
});
// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    
    // Companies Management
    Route::resource('companies', \App\Http\Controllers\CompanyController::class);
    
    // Tenders Management
    Route::resource('tenders', \App\Http\Controllers\TenderController::class);
    Route::post('tenders/{tender}/go-decision', [\App\Http\Controllers\TenderController::class, 'goDecision'])->name('tenders.go-decision');
    Route::post('tenders/{tender}/submit', [\App\Http\Controllers\TenderController::class, 'submit'])->name('tenders.submit');
    Route::post('tenders/{tender}/result', [\App\Http\Controllers\TenderController::class, 'result'])->name('tenders.result');
    Route::post('tenders/{tender}/convert', [\App\Http\Controllers\TenderController::class, 'convert'])->name('tenders.convert');
    Route::get('tenders-pipeline', [\App\Http\Controllers\TenderController::class, 'pipeline'])->name('tenders.pipeline');
    Route::get('tenders-statistics', [\App\Http\Controllers\TenderController::class, 'statistics'])->name('tenders.statistics');
    Route::get('tenders-calendar', [\App\Http\Controllers\TenderController::class, 'calendar'])->name('tenders.calendar');
    Route::get('tenders-expiring', [\App\Http\Controllers\TenderController::class, 'expiring'])->name('tenders.expiring');
});