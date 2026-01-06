<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TenderController;
use App\Http\Controllers\TenderActivityController;

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
    // Tender Activities Routes
Route::prefix('tenders/{tender}')->group(function () {
    Route::get('activities', [TenderActivityController::class, 'index'])
        ->name('tender-activities.index');
    Route::get('activities/create', [TenderActivityController:: class, 'create'])
        ->name('tender-activities.create');
    Route::post('activities', [TenderActivityController::class, 'store'])
        ->name('tender-activities.store');
    Route::get('activities/gantt', [TenderActivityController::class, 'gantt'])
        ->name('tender-activities.gantt');
    Route::get('activities/cpm-analysis', [TenderActivityController::class, 'cpmAnalysis'])
        ->name('tender-activities.cpm-analysis');
    Route::post('activities/recalculate-cpm', [TenderActivityController::class, 'recalculateCPM'])
        ->name('tender-activities.recalculate-cpm');
    Route::delete('activities/{id}', [TenderActivityController:: class, 'destroy'])
        ->name('tender-activities.destroy');
});

// Tender Activities - Edit & Update (without tender prefix)
Route::get('tender-activities/{id}/edit', [TenderActivityController::class, 'edit'])
    ->name('tender-activities. edit');
Route::put('tender-activities/{id}', [TenderActivityController::class, 'update'])
    ->name('tender-activities.update');
Route::get('tender-activities/{tender}/{id}', [TenderActivityController::class, 'show'])
    ->name('tender-activities.show');
    
    // Tender Activities Management
    Route::prefix('tenders/{tender}')->group(function () {
        Route::get('activities', [TenderActivityController::class, 'index'])->name('tender-activities.index');
        Route::get('activities/create', [TenderActivityController::class, 'create'])->name('tender-activities.create');
        Route::post('activities', [TenderActivityController::class, 'store'])->name('tender-activities.store');
        Route::get('activities/{activity}', [TenderActivityController::class, 'show'])->name('tender-activities.show');
        Route::get('activities/{activity}/edit', [TenderActivityController::class, 'edit'])->name('tender-activities.edit');
        Route::put('activities/{activity}', [TenderActivityController::class, 'update'])->name('tender-activities.update');
        Route::delete('activities/{activity}', [TenderActivityController::class, 'destroy'])->name('tender-activities.destroy');
        
        // Gantt Chart
        Route::get('gantt', [TenderActivityController::class, 'gantt'])->name('tender-activities.gantt');
        
        // CPM Analysis
        Route::get('cpm-analysis', [TenderActivityController::class, 'cpmAnalysis'])->name('tender-activities.cpm-analysis');
        Route::post('recalculate-cpm', [TenderActivityController::class, 'recalculateCPM'])->name('tender-activities.recalculate-cpm');
    });
});
