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
    
    // Claims Management
    Route::resource('claims', \App\Http\Controllers\ClaimController::class);
    Route::post('claims/{claim}/send-notice', [\App\Http\Controllers\ClaimController::class, 'sendNotice'])->name('claims.send-notice');
    Route::post('claims/{claim}/submit', [\App\Http\Controllers\ClaimController::class, 'submit'])->name('claims.submit');
    Route::post('claims/{claim}/resolve', [\App\Http\Controllers\ClaimController::class, 'resolve'])->name('claims.resolve');
    Route::get('claims/{claim}/export', [\App\Http\Controllers\ClaimController::class, 'export'])->name('claims.export');
    Route::get('projects/{project}/claims', [\App\Http\Controllers\ClaimController::class, 'projectClaims'])->name('projects.claims');
    Route::get('claims-statistics', [\App\Http\Controllers\ClaimController::class, 'statistics'])->name('claims.statistics');
});