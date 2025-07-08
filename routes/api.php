<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PatientController;
use App\Http\Controllers\Api\HospitalController;
use App\Http\Controllers\Api\AmbulanceController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\TriageController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function() {
    // Rotas públicas
    Route::post('auth/login', [AuthController::class, 'login']);
    Route::post('triage', [TriageController::class, 'triage']);

    // Rotas protegidas por autenticação
    Route::middleware('auth:sanctum')->group(function() {
        
        // Autenticação
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('auth/me', [AuthController::class, 'me']);
        Route::post('auth/refresh', [AuthController::class, 'refresh']);
        Route::post('auth/register', [AuthController::class, 'register'])
            ->middleware('role:administrador');

        // Pacientes
        Route::apiResource('patients', PatientController::class)
            ->middleware('ability:view-pacientes,create-pacientes,edit-pacientes');
        Route::post('patients/triage', [PatientController::class, 'triage'])
            ->middleware('ability:create-pacientes');

        // Hospitais
        Route::apiResource('hospitals', HospitalController::class)
            ->middleware('ability:view-estabelecimentos,manage-estabelecimentos');
        Route::get('hospitals/nearby', [HospitalController::class, 'nearby']);

        // Ambulâncias
        Route::apiResource('ambulances', AmbulanceController::class)
            ->middleware('ability:view-ambulances,manage-veiculos');
        Route::patch('ambulances/{id}/status', [AmbulanceController::class, 'updateStatus'])
            ->middleware('ability:update-ambulance-status');

        // Relatórios
        Route::prefix('reports')->middleware('ability:view-relatorios')->group(function() {
            Route::get('cases-by-region', [ReportController::class, 'casesByRegion']);
            Route::get('evolution', [ReportController::class, 'evolution']);
            Route::get('dashboard', [ReportController::class, 'dashboard']);
        });

        // Rotas específicas por papel
        Route::middleware('role:administrador,gestor')->group(function() {
            Route::get('users', [AuthController::class, 'users']);
            Route::get('statistics', [ReportController::class, 'statistics']);
        });

        Route::middleware('role:condutor')->group(function() {
            Route::get('missions', [AmbulanceController::class, 'missions']);
            Route::post('missions/{id}/accept', [AmbulanceController::class, 'acceptMission']);
        });
    });
});


use App\Http\Controllers\Api\DispatchController;

Route::prefix('v1')->group(function() {
    // Rotas públicas
    Route::post('auth/login', [AuthController::class, 'login']);
    Route::post('triage', [TriageController::class, 'triage']);

    // Rotas protegidas por autenticação
    Route::middleware('auth:sanctum')->group(function() {
        
        // Autenticação
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('auth/me', [AuthController::class, 'me']);
        Route::post('auth/refresh', [AuthController::class, 'refresh']);
        Route::post('auth/register', [AuthController::class, 'register'])
            ->middleware('ability:manage-users');

        // Pacientes
        Route::apiResource('patients', PatientController::class)
            ->middleware('ability:view-pacientes,create-pacientes,edit-pacientes');
        Route::post('patients/triage', [PatientController::class, 'triage'])
            ->middleware('ability:create-pacientes');

        // Hospitais
        Route::apiResource('hospitals', HospitalController::class)
            ->middleware('ability:view-estabelecimentos,manage-estabelecimentos');
        Route::get('hospitals/nearby', [HospitalController::class, 'nearby']);

        // Ambulâncias
        Route::apiResource('ambulances', AmbulanceController::class)
            ->middleware('ability:view-ambulances,manage-veiculos');
        Route::patch('ambulances/{id}/status', [AmbulanceController::class, 'updateStatus'])
            ->middleware('ability:update-ambulance-status');

        // Redirecionamento Inteligente de Ambulâncias
        Route::prefix('dispatch')->middleware('ability:create-pacientes')->group(function() {
            Route::post('/', [DispatchController::class, 'dispatch']);
            Route::get('ambulances/status', [DispatchController::class, 'ambulanceStatus']);
            Route::patch('ambulances/{id}/location', [DispatchController::class, 'updateLocation'])
                ->middleware('ability:update-ambulance-status');
        });

        // Relatórios
        Route::prefix('reports')->middleware('ability:view-relatorios')->group(function() {
            Route::get('cases-by-region', [ReportController::class, 'casesByRegion']);
            Route::get('evolution', [ReportController::class, 'evolution']);
            Route::get('dashboard', [ReportController::class, 'dashboard']);
        });

        // Rotas específicas por papel
        Route::middleware('ability:manage-users')->group(function() {
            Route::get('users', [AuthController::class, 'users']);
        });

        Route::middleware('ability:view-all-data')->group(function() {
            Route::get('statistics', [ReportController::class, 'statistics']);
        });
    });
});
