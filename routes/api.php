<?php

use App\Http\Controllers\Api\BranchController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->group(function () {
    // Branch API endpoints
    Route::apiResource('branches', BranchController::class);
    Route::get('branches/{branch}/users', [BranchController::class, 'users']);
});
