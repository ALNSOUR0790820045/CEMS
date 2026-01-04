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
    
    // Labor Management
    Route::prefix('labor')->name('labor.')->group(function () {
        Route::get('/', [\App\Http\Controllers\LaborController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\LaborController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\LaborController::class, 'store'])->name('store');
        Route::get('/{laborer}', [\App\Http\Controllers\LaborController::class, 'show'])->name('show');
        Route::get('/{laborer}/edit', [\App\Http\Controllers\LaborController::class, 'edit'])->name('edit');
        Route::put('/{laborer}', [\App\Http\Controllers\LaborController::class, 'update'])->name('update');
        Route::delete('/{laborer}', [\App\Http\Controllers\LaborController::class, 'destroy'])->name('destroy');
        
        // Assignment
        Route::post('/{laborer}/assign', [\App\Http\Controllers\LaborController::class, 'assign'])->name('assign');
        
        // Attendance
        Route::get('/attendance/form', [\App\Http\Controllers\LaborController::class, 'attendance'])->name('attendance');
        Route::post('/attendance', [\App\Http\Controllers\LaborController::class, 'storeAttendance'])->name('attendance.store');
        Route::get('/attendance/{date}', [\App\Http\Controllers\LaborController::class, 'attendanceByDate'])->name('attendance.by-date');
        
        // Timesheets
        Route::get('/timesheets/list', [\App\Http\Controllers\LaborController::class, 'timesheets'])->name('timesheets');
        Route::post('/timesheets', [\App\Http\Controllers\LaborController::class, 'storeTimesheet'])->name('timesheets.store');
        Route::post('/timesheets/{timesheet}/approve', [\App\Http\Controllers\LaborController::class, 'approveTimesheet'])->name('timesheets.approve');
        
        // Productivity
        Route::get('/productivity/form', [\App\Http\Controllers\LaborController::class, 'productivity'])->name('productivity');
        Route::post('/productivity', [\App\Http\Controllers\LaborController::class, 'storeProductivity'])->name('productivity.store');
        
        // Statistics & Reports
        Route::get('/statistics', [\App\Http\Controllers\LaborController::class, 'statistics'])->name('statistics');
        Route::get('/expiring-documents', [\App\Http\Controllers\LaborController::class, 'expiringDocuments'])->name('expiring-documents');
        Route::get('/reports', [\App\Http\Controllers\LaborController::class, 'reports'])->name('reports');
    });
    
    // Project Laborers
    Route::get('/projects/{project}/laborers', [\App\Http\Controllers\LaborController::class, 'projectLaborers'])->name('projects.laborers');
});