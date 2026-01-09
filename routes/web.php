<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ProjectActivityController;
use App\Http\Controllers\ActivityDependencyController;
use App\Http\Controllers\ProjectMilestoneController;

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
    Route::resource('companies', CompanyController::class);
    
    // Activities Management
    Route::resource('activities', ProjectActivityController::class);
    Route::get('activities/{activity}/progress', [ProjectActivityController::class, 'progressUpdate'])->name('activities.progress-update');
    Route::put('activities/{activity}/progress', [ProjectActivityController::class, 'updateProgress'])->name('activities.update-progress');
    
    // Dependencies Management
    Route::get('dependencies', [ActivityDependencyController::class, 'index'])->name('dependencies.index');
    Route::post('dependencies', [ActivityDependencyController::class, 'store'])->name('dependencies.store');
    Route::delete('dependencies/{dependency}', [ActivityDependencyController::class, 'destroy'])->name('dependencies.destroy');
    
    // Milestones Management
    Route::get('milestones', [ProjectMilestoneController::class, 'index'])->name('milestones.index');
    Route::post('milestones', [ProjectMilestoneController::class, 'store'])->name('milestones.store');
    Route::put('milestones/{milestone}', [ProjectMilestoneController::class, 'update'])->name('milestones.update');
    Route::delete('milestones/{milestone}', [ProjectMilestoneController::class, 'destroy'])->name('milestones.destroy');
});
