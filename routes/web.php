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
    
    // Tenders Management
    Route::get('/tenders/dashboard', [\App\Http\Controllers\TenderController::class, 'dashboard'])->name('tenders.dashboard');
    Route::get('/tenders/{tender}/decision', [\App\Http\Controllers\TenderController::class, 'decision'])->name('tenders.decision');
    Route::post('/tenders/{tender}/decision', [\App\Http\Controllers\TenderController::class, 'storeDecision'])->name('tenders.decision.store');
    Route::get('/tenders/{tender}/site-visit', [\App\Http\Controllers\TenderController::class, 'siteVisit'])->name('tenders.site-visit');
    Route::post('/tenders/{tender}/site-visit', [\App\Http\Controllers\TenderController::class, 'storeSiteVisit'])->name('tenders.site-visit.store');
    Route::get('/tenders/{tender}/competitors', [\App\Http\Controllers\TenderController::class, 'competitors'])->name('tenders.competitors');
    Route::post('/tenders/{tender}/competitors', [\App\Http\Controllers\TenderController::class, 'storeCompetitor'])->name('tenders.competitors.store');
    Route::resource('tenders', \App\Http\Controllers\TenderController::class);
});