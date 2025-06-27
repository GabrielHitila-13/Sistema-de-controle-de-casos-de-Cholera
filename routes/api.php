<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function() {
    // Rotas públicas (login, registro, triagem inicial…)
    Route::post('auth/login',    [AuthController::class, 'login']);
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('triage',        [TriageController::class, 'triage']); 

    // Rotas protegidas por token
    Route::middleware('auth:sanctum')->group(function() {
        Route::apiResource('provinces',     ProvinceController::class);
        Route::apiResource('municipalities',MunicipalityController::class);
        Route::apiResource('hospitals',     HospitalController::class);
        Route::apiResource('patients',      PatientController::class);
        Route::apiResource('ambulances',    AmbulanceController::class);
        Route::get('reports/cases-by-region', [ReportController::class, 'casesByRegion']);
        Route::get('reports/evolution',       [ReportController::class, 'evolution']);
        Route::post('auth/logout',            [AuthController::class, 'logout']);
    });
});
