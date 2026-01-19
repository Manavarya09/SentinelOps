<?php

use App\Http\Controllers\Api\V1\MonitorController;
use App\Http\Controllers\Api\V1\IncidentController;
use App\Http\Controllers\Api\V1\OrganizationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware(['auth:sanctum'])->group(function () {
    // Organizations
    Route::apiResource('organizations', OrganizationController::class);

    // Monitors
    Route::apiResource('monitors', MonitorController::class);

    // Incidents
    Route::apiResource('incidents', IncidentController::class)->only(['index', 'show', 'update']);
    Route::post('incidents/{incident}/resolve', [IncidentController::class, 'resolve']);

    // Status page (public)
    Route::get('status/{organization}', [OrganizationController::class, 'status']);
});