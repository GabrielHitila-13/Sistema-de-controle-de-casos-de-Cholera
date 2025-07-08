<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PatientController;
use App\Http\Controllers\Api\HospitalController;
use App\Http\Controllers\Api\AmbulanceController;
use App\Http\Controllers\Api\TriageController;
use App\Http\Controllers\Api\DispatchController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Rotas públicas de autenticação
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
});

// Rotas protegidas por autenticação
Route::middleware('auth:sanctum')->group(function () {

    // Autenticação
    Route::prefix('auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
    });

    // Dashboard API routes
    Route::prefix('dashboard')->group(function () {
        Route::get('/stats', [DashboardController::class, 'getStats']);
        Route::get('/evolution-data', [DashboardController::class, 'getEvolutionData']);
        Route::get('/recent-patients', [DashboardController::class, 'getRecentPatients']);
        Route::get('/ambulance-data', [DashboardController::class, 'getAmbulanceData']);
        Route::get('/diagnosis-data', [DashboardController::class, 'getDiagnosisData']);
        Route::get('/high-risk-patients', [DashboardController::class, 'getHighRiskPatients']);
        Route::post('/clear-cache', [DashboardController::class, 'clearCache']);
    });

    // Pacientes
    Route::prefix('patients')->middleware('check.token.ability:patient-access')->group(function () {
        Route::get('/', [PatientController::class, 'index'])->middleware('check.token.ability:view-patients');
        Route::post('/', [PatientController::class, 'store'])->middleware('check.token.ability:create-patients');
        Route::get('/{id}', [PatientController::class, 'show'])->middleware('check.token.ability:view-patients');
        Route::put('/{id}', [PatientController::class, 'update'])->middleware('check.token.ability:edit-patients');
        Route::delete('/{id}', [PatientController::class, 'destroy'])->middleware('check.token.ability:delete-patients');
        Route::get('/{id}/qr-code', [PatientController::class, 'generateQRCode'])->middleware('check.token.ability:view-patients');
    });
    // Patient management (apiResource)
    Route::apiResource('patients', PatientController::class);
    Route::post('/patients/{patient}/triage', [TriageController::class, 'performTriage']);

    // Hospitais/Estabelecimentos
    Route::prefix('hospitals')->middleware('check.token.ability:hospital-access')->group(function () {
        Route::get('/', [HospitalController::class, 'index'])->middleware('check.token.ability:view-hospitals');
        Route::post('/', [HospitalController::class, 'store'])->middleware('check.token.ability:create-hospitals');
        Route::get('/{id}', [HospitalController::class, 'show'])->middleware('check.token.ability:view-hospitals');
        Route::put('/{id}', [HospitalController::class, 'update'])->middleware('check.token.ability:edit-hospitals');
        Route::delete('/{id}', [HospitalController::class, 'destroy'])->middleware('check.token.ability:delete-hospitals');
        Route::get('/{id}/capacity', [HospitalController::class, 'getCapacity'])->middleware('check.token.ability:view-hospitals');
        Route::post('/{id}/update-capacity', [HospitalController::class, 'updateCapacity'])->middleware('check.token.ability:edit-hospitals');
    });
    // Hospital management (apiResource)
    Route::apiResource('hospitals', HospitalController::class);
    Route::get('/hospitals/{hospital}/capacity', [HospitalController::class, 'getCapacity']);

    // Ambulâncias
    Route::prefix('ambulances')->middleware('check.token.ability:ambulance-access')->group(function () {
        Route::get('/', [AmbulanceController::class, 'index'])->middleware('check.token.ability:view-ambulances');
        Route::post('/', [AmbulanceController::class, 'store'])->middleware('check.token.ability:create-ambulances');
        Route::get('/{id}', [AmbulanceController::class, 'show'])->middleware('check.token.ability:view-ambulances');
        Route::put('/{id}', [AmbulanceController::class, 'update'])->middleware('check.token.ability:edit-ambulances');
        Route::delete('/{id}', [AmbulanceController::class, 'destroy'])->middleware('check.token.ability:delete-ambulances');
        Route::post('/{id}/update-location', [AmbulanceController::class, 'updateLocation'])->middleware('check.token.ability:update-location');
        Route::post('/{id}/update-status', [AmbulanceController::class, 'updateStatus'])->middleware('check.token.ability:update-status');
        Route::get('/nearby/{lat}/{lng}', [AmbulanceController::class, 'findNearby'])->middleware('check.token.ability:view-ambulances');
    });
    // Ambulance management (apiResource)
    Route::apiResource('ambulances', AmbulanceController::class);
    Route::patch('/ambulances/{ambulance}/status', [AmbulanceController::class, 'updateStatus']);
    Route::get('/ambulances/available/nearby', [AmbulanceController::class, 'getNearbyAvailable']);

    // Triagem
    Route::prefix('triage')->middleware('check.token.ability:triage-access')->group(function () {
        Route::post('/evaluate', [TriageController::class, 'evaluate'])->middleware('check.token.ability:perform-triage');
        Route::post('/cholera-detection', [TriageController::class, 'detectCholera'])->middleware('check.token.ability:perform-triage');
        Route::get('/patient/{id}', [TriageController::class, 'getPatientTriage'])->middleware('check.token.ability:view-triage');
        Route::post('/patient/{id}/update', [TriageController::class, 'updateTriage'])->middleware('check.token.ability:perform-triage');
    });
    // Triage system
    Route::post('/triage/evaluate', [TriageController::class, 'evaluate']);
    Route::get('/triage/statistics', [TriageController::class, 'getStatistics']);

    // Despacho de Ambulâncias
    Route::prefix('dispatch')->middleware('check.token.ability:dispatch-access')->group(function () {
        Route::post('/request', [DispatchController::class, 'requestAmbulance'])->middleware('check.token.ability:request-ambulance');
        Route::post('/assign', [DispatchController::class, 'assignAmbulance'])->middleware('check.token.ability:assign-ambulance');
        Route::get('/active', [DispatchController::class, 'getActiveDispatches'])->middleware('check.token.ability:view-dispatches');
        Route::post('/{id}/complete', [DispatchController::class, 'completeDispatch'])->middleware('check.token.ability:complete-dispatch');
        Route::post('/{id}/cancel', [DispatchController::class, 'cancelDispatch'])->middleware('check.token.ability:cancel-dispatch');
    });
    // Dispatch system
    Route::post('/dispatch/ambulance', [DispatchController::class, 'dispatchAmbulance']);
    Route::get('/dispatch/status/{dispatch}', [DispatchController::class, 'getStatus']);
    Route::patch('/dispatch/{dispatch}/update', [DispatchController::class, 'updateStatus']);

    // Relatórios
    Route::prefix('reports')->middleware('check.token.ability:report-access')->group(function () {
        Route::get('/dashboard', [ReportController::class, 'getDashboardData'])->middleware('check.token.ability:view-reports');
        Route::get('/patients', [ReportController::class, 'getPatientsReport'])->middleware('check.token.ability:view-reports');
        Route::get('/hospitals', [ReportController::class, 'getHospitalsReport'])->middleware('check.token.ability:view-reports');
        Route::get('/ambulances', [ReportController::class, 'getAmbulancesReport'])->middleware('check.token.ability:view-reports');
        Route::get('/cholera', [ReportController::class, 'getCholeraReport'])->middleware('check.token.ability:view-reports');
        Route::post('/export', [ReportController::class, 'exportReport'])->middleware('check.token.ability:export-reports');
    });

    // Rotas específicas para condutores
    Route::prefix('driver')->middleware('check.role:condutor')->group(function () {
        Route::get('/ambulance', [AmbulanceController::class, 'getMyAmbulance']);
        Route::post('/location', [AmbulanceController::class, 'updateMyLocation']);
        Route::get('/missions', [DispatchController::class, 'getMyMissions']);
        Route::post('/mission/{id}/accept', [DispatchController::class, 'acceptMission']);
        Route::post('/mission/{id}/complete', [DispatchController::class, 'completeMission']);
    });

});

// Rota para obter usuário autenticado
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
