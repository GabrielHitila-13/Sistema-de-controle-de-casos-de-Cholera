<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Paciente;
use App\Models\Estabelecimento;
use App\Models\Veiculo;
use App\Models\User;
use App\Services\ColeraDetectionService;
use App\Services\DashboardUpdateService;
use Carbon\Carbon;

class DashboardController extends Controller
{
    protected $coleraService;
    protected $updateService;

    public function __construct(ColeraDetectionService $coleraService, DashboardUpdateService $updateService)
    {
        $this->coleraService = $coleraService;
        $this->updateService = $updateService;
    }

    public function index()
    {
        $user = auth()->user();
        
        // Estatísticas básicas
        $stats = $this->getStats($user);
        
        // Dados para gráficos
        $evolutionData = $this->getEvolutionData();
        $ambulanceData = $this->getAmbulanceData($user);
        
        // Pacientes recentes
        $pacientesRecentes = $this->getPacientesRecentes($user);
        
        // Casos de alto risco
        $pacientesAltoRisco = $this->getPacientesAltoRisco($user);
        
        // Casos confirmados de cólera
        $coleraConfirmados = $this->getColeraConfirmados($user);

        return view('dashboard', compact(
            'stats',
            'evolutionData',
            'ambulanceData',
            'pacientesRecentes',
            'pacientesAltoRisco',
            'coleraConfirmados'
        ));
    }

    private function getStats($user)
    {
        $query = Paciente::query();
        
        // Filtrar por estabelecimento se não for administrador
        if (!$user->temPapel('administrador')) {
            if ($user->estabelecimento_id) {
                $query->where('estabelecimento_id', $user->estabelecimento_id);
            }
        }

        $stats = [
            'pacientes_total' => $query->count(),
            'colera_confirmada' => $query->where('diagnostico_colera', 'confirmado')->count(),
            'colera_provavel' => $query->where('diagnostico_colera', 'provavel')->count(),
            'colera_suspeita' => $query->where('diagnostico_colera', 'suspeito')->count(),
            'colera_descartada' => $query->where('diagnostico_colera', 'descartado')->count(),
            'colera_pendente' => $query->where('diagnostico_colera', 'pendente')->count(),
            'pacientes_alto_risco' => $query->where('risco', 'alto')->count(),
            'pacientes_medio_risco' => $query->where('risco', 'medio')->count(),
            'pacientes_baixo_risco' => $query->where('risco', 'baixo')->count(),
        ];

        // Estatísticas de veículos (apenas para usuários autorizados)
        if ($user->podeGerenciarVeiculos()) {
            $veiculosQuery = Veiculo::query();
            if (!$user->temPapel('administrador') && $user->estabelecimento_id) {
                $veiculosQuery->where('estabelecimento_id', $user->estabelecimento_id);
            }

            $stats['veiculos_total'] = $veiculosQuery->count();
            $stats['veiculos_disponiveis'] = $veiculosQuery->where('status', 'disponivel')->count();
            $stats['veiculos_em_atendimento'] = $veiculosQuery->where('status', 'em_atendimento')->count();
            $stats['veiculos_manutencao'] = $veiculosQuery->where('status', 'manutencao')->count();
            $stats['veiculos_indisponiveis'] = $veiculosQuery->where('status', 'indisponivel')->count();
        }

        return $stats;
    }

    private function getEvolutionData()
    {
        $dias = [];
        $confirmados = [];
        $provaveis = [];
        $suspeitos = [];

        for ($i = 29; $i >= 0; $i--) {
            $data = Carbon::now()->subDays($i);
            $dias[] = $data->format('d/m');

            $confirmados[] = Paciente::where('diagnostico_colera', 'confirmado')
                ->whereDate('data_diagnostico', $data)
                ->count();

            $provaveis[] = Paciente::where('diagnostico_colera', 'provavel')
                ->whereDate('data_diagnostico', $data)
                ->count();

            $suspeitos[] = Paciente::where('diagnostico_colera', 'suspeito')
                ->whereDate('data_diagnostico', $data)
                ->count();
        }

        return [
            'labels' => $dias,
            'confirmados' => $confirmados,
            'provaveis' => $provaveis,
            'suspeitos' => $suspeitos,
        ];
    }

    private function getAmbulanceData($user)
    {
        if (!$user->podeGerenciarVeiculos()) {
            return [
                'disponivel' => 0,
                'em_atendimento' => 0,
                'manutencao' => 0,
                'indisponivel' => 0,
            ];
        }

        $query = Veiculo::where('tipo', 'ambulancia');
        
        if (!$user->temPapel('administrador') && $user->estabelecimento_id) {
            $query->where('estabelecimento_id', $user->estabelecimento_id);
        }

        return [
            'disponivel' => $query->where('status', 'disponivel')->count(),
            'em_atendimento' => $query->where('status', 'em_atendimento')->count(),
            'manutencao' => $query->where('status', 'manutencao')->count(),
            'indisponivel' => $query->where('status', 'indisponivel')->count(),
        ];
    }

    private function getPacientesRecentes($user)
    {
        $query = Paciente::with('estabelecimento')
            ->where('created_at', '>=', now()->subDay())
            ->orderBy('created_at', 'desc');

        if (!$user->temPapel('administrador') && $user->estabelecimento_id) {
            $query->where('estabelecimento_id', $user->estabelecimento_id);
        }

        return $query->limit(10)->get();
    }

    private function getPacientesAltoRisco($user)
    {
        $query = Paciente::with('estabelecimento')
            ->where('risco', 'alto')
            ->orderBy('data_triagem', 'desc');

        if (!$user->temPapel('administrador') && $user->estabelecimento_id) {
            $query->where('estabelecimento_id', $user->estabelecimento_id);
        }

        return $query->limit(5)->get();
    }

    private function getColeraConfirmados($user)
    {
        $query = Paciente::with('estabelecimento')
            ->where('diagnostico_colera', 'confirmado')
            ->where('data_diagnostico', '>=', now()->subDay())
            ->orderBy('data_diagnostico', 'desc');

        if (!$user->temPapel('administrador') && $user->estabelecimento_id) {
            $query->where('estabelecimento_id', $user->estabelecimento_id);
        }

        return $query->limit(5)->get();
    }

    public function stats(Request $request)
    {
        $user = auth()->user();
        $stats = $this->getStats($user);

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
