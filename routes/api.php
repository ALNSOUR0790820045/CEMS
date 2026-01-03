<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\GRNController;

Route::middleware(['auth:sanctum'])->group(function () {
    // GRN Routes
    Route::apiResource('grns', GRNController::class);
    Route::post('grns/{id}/inspect', [GRNController::class, 'inspect'])->name('grns.inspect');
    Route::post('grns/{id}/accept', [GRNController::class, 'accept'])->name('grns.accept');
    Route::get('grns/pending-inspection', [GRNController::class, 'pendingInspection'])->name('grns.pending-inspection');
});
