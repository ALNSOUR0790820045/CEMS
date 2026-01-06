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
    
    // Companies Management - with permission middleware
    Route::middleware('permission:companies.view')->group(function () {
        Route::get('/companies', [\App\Http\Controllers\CompanyController::class, 'index'])->name('companies.index');
        Route::get('/companies/{company}', [\App\Http\Controllers\CompanyController::class, 'show'])->name('companies.show');
    });
    Route::get('/companies/create', [\App\Http\Controllers\CompanyController:: class, 'create'])
        ->name('companies.create')->middleware('permission:companies.create');
    Route::post('/companies', [\App\Http\Controllers\CompanyController::class, 'store'])
        ->name('companies. store')->middleware('permission:companies. create');
    Route::get('/companies/{company}/edit', [\App\Http\Controllers\CompanyController::class, 'edit'])
        ->name('companies.edit')->middleware('permission:companies.edit');
    Route::put('/companies/{company}', [\App\Http\Controllers\CompanyController:: class, 'update'])
        ->name('companies.update')->middleware('permission:companies.edit');
    Route::delete('/companies/{company}', [\App\Http\Controllers\CompanyController::class, 'destroy'])
        ->name('companies.destroy')->middleware('permission:companies.delete');
    
    // Branches Management - with permission middleware
    Route::middleware('permission:branches.view')->group(function () {
        Route::get('/branches', [\App\Http\Controllers\BranchController::class, 'index'])->name('branches.index');
        Route::get('/branches/{branch}', [\App\Http\Controllers\BranchController::class, 'show'])->name('branches.show');
    });
    Route::get('/branches/create', [\App\Http\Controllers\BranchController::class, 'create'])
        ->name('branches. create')->middleware('permission:branches. create');
    Route::post('/branches', [\App\Http\Controllers\BranchController::class, 'store'])
        ->name('branches.store')->middleware('permission:branches.create');
    Route::get('/branches/{branch}/edit', [\App\Http\Controllers\BranchController::class, 'edit'])
        ->name('branches.edit')->middleware('permission:branches.edit');
    Route::put('/branches/{branch}', [\App\Http\Controllers\BranchController::class, 'update'])
        ->name('branches.update')->middleware('permission:branches.edit');
    Route::delete('/branches/{branch}', [\App\Http\Controllers\BranchController::class, 'destroy')
        ->name('branches.destroy')->middleware('permission:branches.delete');
    
    // Users Management - with permission middleware
    Route:: middleware('permission:users.view')->group(function () {
        Route::get('/users', [\App\Http\Controllers\UserController:: class, 'index'])->name('users.index');
        Route::get('/users/{user}', [\App\Http\Controllers\UserController::class, 'show'])->name('users.show');
    });
    Route::get('/users/create', [\App\Http\Controllers\UserController:: class, 'create'])
        ->name('users.create')->middleware('permission:users.create');
    Route::post('/users', [\App\Http\Controllers\UserController::class, 'store'])
        ->name('users.store')->middleware('permission:users.create');
    Route::get('/users/{user}/edit', [\App\Http\Controllers\UserController:: class, 'edit'])
        ->name('users.edit')->middleware('permission:users.edit');
    Route::put('/users/{user}', [\App\Http\Controllers\UserController::class, 'update'])
        ->name('users.update')->middleware('permission:users.edit');
    Route::delete('/users/{user}', [\App\Http\Controllers\UserController::class, 'destroy'])
        ->name('users.destroy')->middleware('permission:users.delete');
    
    // Roles & Permissions Management - with permission middleware
    Route::middleware('permission: roles.view')->group(function () {
        Route::get('/roles', [\App\Http\Controllers\RoleController::class, 'index'])->name('roles.index');
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
    Route::delete('/roles/{role}', [\App\Http\Controllers\RoleController::class, 'destroy'])
        ->name('roles.destroy')->middleware('permission:roles.delete');
});