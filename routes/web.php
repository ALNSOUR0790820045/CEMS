<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;

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
    
    // Projects Management
    Route::resource('projects', ProjectController::class);
    Route::get('/projects/{project}/dashboard', [ProjectController::class, 'dashboard'])->name('projects.dashboard');
    Route::get('/projects/{project}/progress', [ProjectController::class, 'progress'])->name('projects.progress');
    Route::post('/projects/{project}/progress', [ProjectController::class, 'storeProgress'])->name('projects.progress.store');
    Route::get('/projects/{project}/team', [ProjectController::class, 'team'])->name('projects.team');
    Route::get('/projects/{project}/milestones', [ProjectController::class, 'milestones'])->name('projects.milestones');
    Route::get('/projects/{project}/issues', [ProjectController::class, 'issues'])->name('projects.issues');
    Route::get('/portfolio', [ProjectController::class, 'portfolio'])->name('projects.portfolio');
    Route::get('/api/projects/statistics', [ProjectController::class, 'statistics'])->name('projects.statistics');
});