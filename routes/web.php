<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\EstabelecimentoController;
use App\Http\Controllers\GabineteController;
use App\Http\Controllers\VeiculoController;
use App\Http\Controllers\PontoAtendimentoController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Support\Facades\Route;

// Rota raiz redireciona para login
Route::get('/', function () {
    return redirect()->route('login');
});

// Rotas de autenticação
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
    
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Pacientes
    Route::resource('pacientes', PacienteController::class);
    Route::get('/relatorios/pacientes-pdf', [PacienteController::class, 'exportPdf'])->name('relatorios.pacientes.pdf');
    
    // Estabelecimentos
    Route::resource('estabelecimentos', EstabelecimentoController::class);
    
    // Gabinetes
    Route::resource('gabinetes', GabineteController::class);
    
    // Veículos
    Route::resource('veiculos', VeiculoController::class);
    Route::patch('veiculos/{veiculo}/status', [VeiculoController::class, 'updateStatus'])->name('veiculos.update-status');
    
    // Pontos de Atendimento
    Route::resource('pontos-atendimento', PontoAtendimentoController::class);
    
    // API Routes para Dashboard (AJAX)
    Route::get('/api/dashboard/stats', [DashboardController::class, 'getStats'])->name('api.dashboard.stats');
});
