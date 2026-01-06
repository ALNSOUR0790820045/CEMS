<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GuaranteeController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TenderController;
use App\Http\Controllers\TenderActivityController;
use Illuminate\Support\Facades\Route;

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController:: class, 'index'])->name('dashboard');
    Route::post('/logout', [LoginController::  class, 'logout'])->name('logout');
    
    // Companies Management - with permission middleware
    Route::middleware('permission:companies. view')->group(function () {
        Route::get('/companies', [CompanyController::class, 'index'])->name('companies.index');
        Route::get('/companies/{company}', [CompanyController::class, 'show'])->name('companies.show');
    });
    Route::get('/companies/create', [CompanyController:: class, 'create'])
        ->name('companies.create')->middleware('permission:companies.create');
    Route::post('/companies', [CompanyController::class, 'store'])
        ->name('companies. store')->middleware('permission:companies. create');
    Route::get('/companies/{company}/edit', [CompanyController::class, 'edit'])
        ->name('companies. edit')->middleware('permission:companies. edit');
    Route::put('/companies/{company}', [CompanyController:: class, 'update'])
        ->name('companies.update')->middleware('permission:companies.edit');
    Route::delete('/companies/{company}', [CompanyController::class, 'destroy'])
        ->name('companies.destroy')->middleware('permission:companies.delete');
    
    // Branches Management - with permission middleware
    Route:: middleware('permission:branches.view')->group(function () {
        Route::get('/branches', [\App\Http\Controllers\BranchController:: class, 'index'])->name('branches.index');
        Route::get('/branches/{branch}', [\App\Http\Controllers\BranchController::class, 'show'])->name('branches.show');
    });
    Route::get('/branches/create', [\App\Http\Controllers\BranchController::class, 'create'])
        ->name('branches.create')->middleware('permission:branches.create');
    Route::post('/branches', [\App\Http\Controllers\BranchController:: class, 'store'])
        ->name('branches.store')->middleware('permission:branches.create');
    Route::get('/branches/{branch}/edit', [\App\Http\Controllers\BranchController:: class, 'edit'])
        ->name('branches.edit')->middleware('permission:branches.edit');
    Route::put('/branches/{branch}', [\App\Http\Controllers\BranchController::class, 'update'])
        ->name('branches. update')->middleware('permission:branches. edit');
    Route::delete('/branches/{branch}', [\App\Http\Controllers\BranchController::class, 'destroy'])
        ->name('branches.destroy')->middleware('permission:branches.delete');
    
    // Users Management - with permission middleware
    Route:: middleware('permission: users.view')->group(function () {
        Route::get('/users', [\App\Http\Controllers\UserController:: class, 'index'])->name('users.index');
        Route::get('/users/{user}', [\App\Http\Controllers\UserController::class, 'show'])->name('users.show');
    });
    Route::get('/users/create', [\App\Http\Controllers\UserController:: class, 'create'])
        ->name('users.create')->middleware('permission:users.create');
    Route::post('/users', [\App\Http\Controllers\UserController::class, 'store'])
        ->name('users.store')->middleware('permission:users.create');
    Route::get('/users/{user}/edit', [\App\Http\Controllers\UserController:: class, 'edit'])
        ->name('users. edit')->middleware('permission:users. edit');
    Route::put('/users/{user}', [\App\Http\Controllers\UserController::  class, 'update'])
        ->name('users.update')->middleware('permission:users.edit');
    Route::delete('/users/{user}', [\App\Http\Controllers\UserController::class, 'destroy'])
        ->name('users.destroy')->middleware('permission:users.delete');
    
    // Roles & Permissions Management - with permission middleware
    Route::middleware('permission: roles.view')->group(function () {
        Route::get('/roles', [\App\Http\Controllers\RoleController:: class, 'index'])->name('roles.index');
        Route::get('/roles/{role}', [\App\Http\Controllers\RoleController::class, 'show'])->name('roles.show');
    });
    Route::get('/roles/create', [\App\Http\Controllers\RoleController::class, 'create'])
        ->name('roles.create')->middleware('permission:roles.create');
    Route::post('/roles', [\App\Http\Controllers\RoleController::class, 'store'])
        ->name('roles.store')->middleware('permission:roles.create');
    Route::get('/roles/{role}/edit', [\App\Http\Controllers\RoleController::class, 'edit'])
        ->name('roles.edit')->middleware('permission:roles.edit');
    Route::put('/roles/{role}', [\App\Http\Controllers\RoleController::class, 'update'])
        ->name('roles.update')->middleware('permission:roles.edit');
    Route::delete('/roles/{role}', [\App\Http\Controllers\RoleController:: class, 'destroy'])
        ->name('roles.destroy')->middleware('permission:roles.delete');
    
    // Banks Management
    Route::resource('banks', BankController::class);
    
    // Guarantees Management
    Route::resource('guarantees', GuaranteeController::class);
    Route::post('guarantees/{guarantee}/approve', [GuaranteeController::class, 'approve'])->name('guarantees.approve');
    Route::get('guarantees/{guarantee}/renew', [GuaranteeController::class, 'showRenewForm'])->name('guarantees.renew');
    Route::post('guarantees/{guarantee}/renew', [GuaranteeController::class, 'renew'])->name('guarantees.renew.store');
    Route::get('guarantees/{guarantee}/release', [GuaranteeController::  class, 'showReleaseForm'])->name('guarantees.release');
    Route::post('guarantees/{guarantee}/release', [GuaranteeController::class, 'release'])->name('guarantees.release. store');
    
    // Guarantees Reports & Statistics
    Route::get('guarantees-expiring', [GuaranteeController:: class, 'expiring'])->name('guarantees.expiring');
    Route::get('guarantees-statistics', [GuaranteeController::class, 'statistics'])->name('guarantees.statistics');
    Route::get('guarantees-reports', [GuaranteeController::class, 'reports'])->name('guarantees.reports');
    
    // Tenders Management
    Route::resource('tenders', TenderController:: class);
    Route::post('tenders/{tender}/go-decision', [TenderController:: class, 'goDecision'])->name('tenders.go-decision');
    Route::post('tenders/{tender}/submit', [TenderController::  class, 'submit'])->name('tenders.submit');
    Route::post('tenders/{tender}/result', [TenderController::  class, 'result'])->name('tenders.result');
    Route::post('tenders/{tender}/convert', [TenderController::  class, 'convert'])->name('tenders.convert');
    Route::get('tenders-pipeline', [TenderController::class, 'pipeline'])->name('tenders.pipeline');
    Route::get('tenders-statistics', [TenderController::class, 'statistics'])->name('tenders.statistics');
    Route::get('tenders-calendar', [TenderController::class, 'calendar'])->name('tenders.calendar');
    Route::get('tenders-expiring', [TenderController::class, 'expiring'])->name('tenders.expiring');
    
    // Tender Activities Management
    Route::prefix('tenders/{tender}')->group(function () {
        Route::get('activities', [TenderActivityController::class, 'index'])->name('tender-activities.index');
        Route::get('activities/create', [TenderActivityController:: class, 'create'])->name('tender-activities.create');
        Route::post('activities', [TenderActivityController::class, 'store'])->name('tender-activities.store');
        Route::get('activities/gantt', [TenderActivityController::class, 'gantt'])->name('tender-activities. gantt');
        Route::get('activities/cpm-analysis', [TenderActivityController::class, 'cpmAnalysis'])->name('tender-activities.cpm-analysis');
        Route::post('activities/recalculate-cpm', [TenderActivityController::class, 'recalculateCPM'])->name('tender-activities.recalculate-cpm');
    });
    
    // Tender Activities - Edit & Update (without tender prefix)
    Route::get('tender-activities/{id}/edit', [TenderActivityController:: class, 'edit'])->name('tender-activities.edit');
    Route::put('tender-activities/{id}', [TenderActivityController::class, 'update'])->name('tender-activities.update');
    Route::get('tender-activities/{tender}/{id}', [TenderActivityController::class, 'show'])->name('tender-activities.show');
    Route::delete('tenders/{tender}/activities/{id}', [TenderActivityController::class, 'destroy'])->name('tender-activities. destroy');
    
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