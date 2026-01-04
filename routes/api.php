<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\UserRoleController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum'])->group(function () {
    
    // Roles Management (requires permission)
    Route::middleware('permission:manage-roles')->group(function () {
        Route::get('/roles', [RoleController::class, 'index']);
        Route::post('/roles', [RoleController::class, 'store']);
        Route::get('/roles/{id}', [RoleController::class, 'show']);
        Route::put('/roles/{id}', [RoleController::class, 'update']);
        Route::post('/roles/{id}/permissions', [RoleController::class, 'assignPermissions']);
    });

    // Permissions Management (requires permission)
    Route::middleware('permission:manage-permissions')->group(function () {
        Route::get('/permissions', [PermissionController::class, 'index']);
        Route::post('/permissions', [PermissionController::class, 'store']);
    });

    // User Role Assignment (requires permission)
    Route::middleware('permission:assign-roles')->group(function () {
        Route::post('/users/{id}/assign-role', [UserRoleController::class, 'assignRole']);
    });
});
