<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\EstabelecimentoController;
use App\Http\Controllers\GabineteController;
use App\Http\Controllers\VeiculoController;
use App\Http\Controllers\PontoAtendimentoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TriagemController;
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

// Rotas autenticadas
Route::middleware(['auth'])->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    
    // Dashboard - Todos os usuários autenticados
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Triagem Inteligente
    Route::prefix('triagem')->name('triagem.')->group(function () {
        Route::get('/', [TriagemController::class, 'index'])->name('index');
        Route::get('/create', [TriagemController::class, 'create'])->name('create');
        Route::post('/store', [TriagemController::class, 'store'])->name('store');
        Route::get('/resultado/{paciente}', [TriagemController::class, 'resultado'])->name('resultado');
        Route::get('/imprimir/{paciente}', [TriagemController::class, 'imprimirFicha'])->name('imprimir-ficha');
        Route::post('/avaliar-sintomas', [TriagemController::class, 'avaliarSintomas'])->name('avaliar-sintomas');
        Route::post('/hospital-proximo', [TriagemController::class, 'buscarHospitalProximo'])->name('hospital-proximo');
    });
    
    // Todas as rotas principais
    Route::resource('usuarios', UserController::class);
    Route::resource('pacientes', PacienteController::class);
    Route::resource('estabelecimentos', EstabelecimentoController::class);
    Route::resource('gabinetes', GabineteController::class);
    Route::resource('veiculos', VeiculoController::class);
    Route::resource('pontos-atendimento', PontoAtendimentoController::class);
    
    // Rotas adicionais
    Route::patch('usuarios/{usuario}/toggle-status', [UserController::class, 'toggleStatus'])->name('usuarios.toggle-status');
    Route::patch('veiculos/{veiculo}/status', [VeiculoController::class, 'updateStatus'])->name('veiculos.update-status');
    Route::get('/relatorios/pacientes-pdf', [PacienteController::class, 'exportPdf'])->name('relatorios.pacientes.pdf');
    
    // API Routes para Dashboard (AJAX)
    Route::get('/api/dashboard/stats', [DashboardController::class, 'getStats'])->name('api.dashboard.stats');
});
