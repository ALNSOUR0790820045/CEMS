<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Progress\ProgressDashboardController;
use App\Http\Controllers\Progress\ProgressUpdateController;
use App\Http\Controllers\Progress\TimesheetController;
use App\Http\Controllers\Progress\BaselineController;
use App\Http\Controllers\Progress\VarianceAnalysisController;
use App\Http\Controllers\Progress\ForecastingController;

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
    
    // Progress Tracking & EVM Routes
    Route::prefix('progress')->group(function () {
        // Dashboard
        Route::get('/dashboard', [ProgressDashboardController::class, 'index'])->name('progress.dashboard');
        Route::get('/dashboard/{project}/chart-data', [ProgressDashboardController::class, 'chartData'])->name('progress.dashboard.chart-data');
        Route::get('/dashboard/{project}/latest-snapshot', [ProgressDashboardController::class, 'latestSnapshot'])->name('progress.dashboard.latest-snapshot');
        
        // Progress Updates
        Route::get('/update/{project}/create', [ProgressUpdateController::class, 'create'])->name('progress.update.create');
        Route::post('/update/{project}', [ProgressUpdateController::class, 'store'])->name('progress.update.store');
        Route::post('/update/{project}/preview', [ProgressUpdateController::class, 'preview'])->name('progress.update.preview');
        Route::get('/update/{project}/history', [ProgressUpdateController::class, 'history'])->name('progress.update.history');
        
        // Timesheets
        Route::get('/timesheets/{project}', [TimesheetController::class, 'index'])->name('progress.timesheets.index');
        Route::post('/timesheets/{project}', [TimesheetController::class, 'store'])->name('progress.timesheets.store');
        Route::post('/timesheets/{project}/bulk', [TimesheetController::class, 'bulkStore'])->name('progress.timesheets.bulk-store');
        Route::post('/timesheets/{timesheet}/submit', [TimesheetController::class, 'submit'])->name('progress.timesheets.submit');
        Route::post('/timesheets/{timesheet}/approve', [TimesheetController::class, 'approve'])->name('progress.timesheets.approve');
        Route::post('/timesheets/{timesheet}/reject', [TimesheetController::class, 'reject'])->name('progress.timesheets.reject');
        Route::get('/timesheets/pending', [TimesheetController::class, 'pending'])->name('progress.timesheets.pending');
        Route::post('/timesheets/export-payroll', [TimesheetController::class, 'exportPayroll'])->name('progress.timesheets.export-payroll');
        
        // Baselines
        Route::get('/baseline/{project}', [BaselineController::class, 'index'])->name('progress.baseline.index');
        Route::post('/baseline/{project}', [BaselineController::class, 'store'])->name('progress.baseline.store');
        Route::post('/baseline/{project}/{baseline}/set-current', [BaselineController::class, 'setCurrent'])->name('progress.baseline.set-current');
        Route::get('/baseline/{project}/{baseline}/compare', [BaselineController::class, 'compare'])->name('progress.baseline.compare');
        Route::post('/baseline/{project}/compare-baselines', [BaselineController::class, 'compareBaselines'])->name('progress.baseline.compare-baselines');
        
        // Variance Analysis
        Route::get('/variance-analysis/{project}', [VarianceAnalysisController::class, 'index'])->name('progress.variance-analysis.index');
        Route::get('/variance-analysis/{project}/activity/{activity}', [VarianceAnalysisController::class, 'activityDetail'])->name('progress.variance-analysis.activity-detail');
        
        // Forecasting
        Route::get('/forecasting/{project}', [ForecastingController::class, 'index'])->name('progress.forecasting.index');
        Route::post('/forecasting/{project}/custom-scenario', [ForecastingController::class, 'customScenario'])->name('progress.forecasting.custom-scenario');
    });
});
