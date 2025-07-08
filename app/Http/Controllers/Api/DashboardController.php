<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Paciente;
use App\Models\Estabelecimento;
use App\Models\Veiculo;
use App\Models\User;
use App\Services\ColeraDetectionService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    protected $coleraService;

    public function __construct(ColeraDetectionService $coleraService)
    {
        $this->coleraService = $coleraService;
    }

    /**
     * Get real-time dashboard statistics
     */
    public function getStats(Request $request)
    {
        $user = auth()->user();
        $cacheKey = "dashboard_stats_" . $user->id;
        
        $stats = Cache::remember($cacheKey, 30, function () use ($user) {
            return $this->calculateStats($user);
        });

        return response()->json([
            'success' => true,
            'data' => $stats,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Get evolution data for charts
     */
    public function getEvolutionData(Request $request)
    {
        $user = auth()->user();
        $days = $request->get('days', 30);
        $cacheKey = "evolution_data_{$user->id}_{$days}";
        
        $data = Cache::remember($cacheKey, 60, function () use ($user, $days) {
            return $this->calculateEvolutionData($user, $days);
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Get recent patients data
     */
    public function getRecentPatients(Request $request)
    {
        $user = auth()->user();
        $limit = $request->get('limit', 10);
        $hours = $request->get('hours', 24);
        
        $query = Paciente::with(['estabelecimento'])
            ->where('created_at', '>=', now()->subHours($hours))
            ->orderBy('created_at', 'desc');

        if (!$user->temPapel('administrador') && $user->estabelecimento_id) {
            $query->where('estabelecimento_id', $user->estabelecimento_id);
        }

        $patients = $query->limit($limit)->get()->map(function ($patient) {
            return [
                'id' => $patient->id,
                'nome' => $patient->nome,
                'risco' => $patient->risco,
                'risco_formatado' => $patient->risco_formatado,
                'diagnostico_colera' => $patient->diagnostico_colera,
                'diagnostico_colera_formatado' => $patient->diagnostico_colera_formatado,
                'estabelecimento' => $patient->estabelecimento ? [
                    'id' => $patient->estabelecimento->id,
                    'nome' => $patient->estabelecimento->nome
                ] : null,
                'created_at' => $patient->created_at->toISOString(),
                'status_urgencia' => $patient->getStatusUrgencia(),
                'cor_status' => $patient->getCorStatus()
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $patients,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Get ambulance status data
     */
    public function getAmbulanceData(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->podeGerenciarVeiculos()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $cacheKey = "ambulance_data_" . $user->id;
        
        $data = Cache::remember($cacheKey, 30, function () use ($user) {
            return $this->calculateAmbulanceData($user);
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Get diagnosis distribution data
     */
    public function getDiagnosisData(Request $request)
    {
        $user = auth()->user();
        $cacheKey = "diagnosis_data_" . $user->id;
        
        $data = Cache::remember($cacheKey, 60, function () use ($user) {
            $query = Paciente::query();
            
            if (!$user->temPapel('administrador') && $user->estabelecimento_id) {
                $query->where('estabelecimento_id', $user->estabelecimento_id);
            }

            return [
                'confirmado' => $query->where('diagnostico_colera', 'confirmado')->count(),
                'provavel' => $query->where('diagnostico_colera', 'provavel')->count(),
                'suspeito' => $query->where('diagnostico_colera', 'suspeito')->count(),
                'descartado' => $query->where('diagnostico_colera', 'descartado')->count(),
                'pendente' => $query->where('diagnostico_colera', 'pendente')->count(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Get high-risk patients
     */
    public function getHighRiskPatients(Request $request)
    {
        $user = auth()->user();
        $limit = $request->get('limit', 5);
        
        $query = Paciente::with(['estabelecimento'])
            ->where('risco', 'alto')
            ->orderBy('data_triagem', 'desc');

        if (!$user->temPapel('administrador') && $user->estabelecimento_id) {
            $query->where('estabelecimento_id', $user->estabelecimento_id);
        }

        $patients = $query->limit($limit)->get()->map(function ($patient) {
            return [
                'id' => $patient->id,
                'nome' => $patient->nome,
                'risco' => $patient->risco,
                'diagnostico_colera' => $patient->diagnostico_colera,
                'estabelecimento' => $patient->estabelecimento ? [
                    'nome' => $patient->estabelecimento->nome
                ] : null,
                'data_triagem' => $patient->data_triagem ? $patient->data_triagem->toISOString() : null,
                'probabilidade_colera' => $patient->probabilidade_colera
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $patients,
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Clear dashboard cache
     */
    public function clearCache(Request $request)
    {
        $user = auth()->user();
        $patterns = [
            "dashboard_stats_{$user->id}",
            "evolution_data_{$user->id}_*",
            "ambulance_data_{$user->id}",
            "diagnosis_data_{$user->id}"
        ];

        foreach ($patterns as $pattern) {
            if (str_contains($pattern, '*')) {
                // Clear pattern-based cache keys
                $keys = Cache::getRedis()->keys(str_replace('*', '*', $pattern));
                if (!empty($keys)) {
                    Cache::getRedis()->del($keys);
                }
            } else {
                Cache::forget($pattern);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Cache cleared successfully'
        ]);
    }

    private function calculateStats($user)
    {
        $query = Paciente::query();
        
        if (!$user->temPapel('administrador') && $user->estabelecimento_id) {
            $query->where('estabelecimento_id', $user->estabelecimento_id);
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

        // Add vehicle statistics if user can manage vehicles
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

    private function calculateEvolutionData($user, $days = 30)
    {
        $labels = [];
        $confirmados = [];
        $provaveis = [];
        $suspeitos = [];

        $baseQuery = Paciente::query();
        if (!$user->temPapel('administrador') && $user->estabelecimento_id) {
            $baseQuery->where('estabelecimento_id', $user->estabelecimento_id);
        }

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $labels[] = $date->format('d/m');

            $confirmados[] = (clone $baseQuery)
                ->where('diagnostico_colera', 'confirmado')
                ->whereDate('data_diagnostico', $date)
                ->count();

            $provaveis[] = (clone $baseQuery)
                ->where('diagnostico_colera', 'provavel')
                ->whereDate('data_diagnostico', $date)
                ->count();

            $suspeitos[] = (clone $baseQuery)
                ->where('diagnostico_colera', 'suspeito')
                ->whereDate('data_diagnostico', $date)
                ->count();
        }

        return [
            'labels' => $labels,
            'confirmados' => $confirmados,
            'provaveis' => $provaveis,
            'suspeitos' => $suspeitos,
        ];
    }

    private function calculateAmbulanceData($user)
    {
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
}
