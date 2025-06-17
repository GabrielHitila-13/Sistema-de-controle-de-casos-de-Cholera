<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use App\Models\Estabelecimento;
use App\Models\Gabinete;
use App\Models\User;
use App\Models\Veiculo;
use App\Models\PontoAtendimento;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Estatísticas básicas
        $stats = [
            'pacientes_total' => Paciente::count(),
            'pacientes_alto_risco' => Paciente::where('risco', 'alto')->count(),
            'pacientes_medio_risco' => Paciente::where('risco', 'medio')->count(),
            'pacientes_baixo_risco' => Paciente::where('risco', 'baixo')->count(),
            'estabelecimentos_total' => Estabelecimento::count(),
            'gabinetes_total' => Gabinete::count(),
        ];

        // Dados específicos baseados no papel do usuário
        if ($user->podeGerenciarUsuarios()) {
            $stats['usuarios_total'] = User::count();
            $stats['usuarios_ativos'] = User::where('ativo', true)->count();
        }

        if ($user->temAlgumPapel(['administrador', 'tecnico'])) {
            $stats['veiculos_disponiveis'] = Veiculo::where('status', 'disponivel')->count();
            $stats['veiculos_total'] = Veiculo::count();
        }

        // Pacientes recentes (últimas 24 horas)
        $pacientesRecentes = Paciente::with('estabelecimento')
            ->where('created_at', '>=', now()->subDay())
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Pacientes de alto risco
        $pacientesAltoRisco = Paciente::with('estabelecimento')
            ->where('risco', 'alto')
            ->orderBy('data_triagem', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard', compact('stats', 'pacientesRecentes', 'pacientesAltoRisco'));
    }

    public function getStats()
    {
        return response()->json([
            'pacientes_total' => Paciente::count(),
            'pacientes_alto_risco' => Paciente::where('risco', 'alto')->count(),
            'pacientes_hoje' => Paciente::whereDate('created_at', today())->count(),
            'estabelecimentos_total' => Estabelecimento::count(),
        ]);
    }
}
