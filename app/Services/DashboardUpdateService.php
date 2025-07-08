<?php

namespace App\Services;

use App\Models\Paciente;
use App\Models\Veiculo;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Event;

class DashboardUpdateService
{
    /**
     * Trigger dashboard update for all users
     */
    public function triggerGlobalUpdate()
    {
        try {
            // Clear all dashboard-related cache
            $this->clearAllDashboardCache();
            
            // Broadcast update event
            Event::dispatch('dashboard.updated', [
                'timestamp' => now()->toISOString(),
                'type' => 'global'
            ]);
            
            Log::info('Dashboard global update triggered successfully');
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to trigger dashboard global update: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Trigger dashboard update for specific establishment
     */
    public function triggerEstablishmentUpdate($estabelecimentoId)
    {
        try {
            // Clear cache for users of this establishment
            $this->clearEstablishmentCache($estabelecimentoId);
            
            // Broadcast update event
            Event::dispatch('dashboard.updated', [
                'timestamp' => now()->toISOString(),
                'type' => 'establishment',
                'establishment_id' => $estabelecimentoId
            ]);
            
            Log::info("Dashboard update triggered for establishment {$estabelecimentoId}");
            
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to trigger dashboard update for establishment {$estabelecimentoId}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Handle patient data changes
     */
    public function handlePatientUpdate($paciente, $action = 'updated')
    {
        try {
            // Clear relevant cache
            if ($paciente->estabelecimento_id) {
                $this->clearEstablishmentCache($paciente->estabelecimento_id);
            } else {
                $this->clearAllDashboardCache();
            }
            
            // Broadcast specific update
            Event::dispatch('dashboard.patient.updated', [
                'patient_id' => $paciente->id,
                'establishment_id' => $paciente->estabelecimento_id,
                'action' => $action,
                'timestamp' => now()->toISOString()
            ]);
            
            Log::info("Patient {$action}: {$paciente->id}");
            
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to handle patient update: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Handle vehicle status changes
     */
    public function handleVehicleUpdate($veiculo, $action = 'updated')
    {
        try {
            // Clear relevant cache
            if ($veiculo->estabelecimento_id) {
                $this->clearEstablishmentCache($veiculo->estabelecimento_id);
            } else {
                $this->clearAllDashboardCache();
            }
            
            // Broadcast specific update
            Event::dispatch('dashboard.vehicle.updated', [
                'vehicle_id' => $veiculo->id,
                'establishment_id' => $veiculo->estabelecimento_id,
                'action' => $action,
                'status' => $veiculo->status,
                'timestamp' => now()->toISOString()
            ]);
            
            Log::info("Vehicle {$action}: {$veiculo->id}");
            
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to handle vehicle update: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get real-time statistics
     */
    public function getRealTimeStats($userId)
    {
        $cacheKey = "realtime_stats_{$userId}";
        
        return Cache::remember($cacheKey, 10, function () use ($userId) {
            $user = \App\Models\User::find($userId);
            if (!$user) {
                return null;
            }

            $query = Paciente::query();
            if (!$user->temPapel('administrador') && $user->estabelecimento_id) {
                $query->where('estabelecimento_id', $user->estabelecimento_id);
            }

            return [
                'total_patients' => $query->count(),
                'confirmed_cholera' => $query->where('diagnostico_colera', 'confirmado')->count(),
                'high_risk' => $query->where('risco', 'alto')->count(),
                'recent_cases' => $query->where('created_at', '>=', now()->subHour())->count(),
                'timestamp' => now()->toISOString()
            ];
        });
    }

    /**
     * Clear all dashboard cache
     */
    private function clearAllDashboardCache()
    {
        $patterns = [
            'dashboard_stats_*',
            'evolution_data_*',
            'ambulance_data_*',
            'diagnosis_data_*',
            'realtime_stats_*'
        ];

        foreach ($patterns as $pattern) {
            try {
                if (config('cache.default') === 'redis') {
                    $keys = Cache::getRedis()->keys($pattern);
                    if (!empty($keys)) {
                        Cache::getRedis()->del($keys);
                    }
                } else {
                    // For other cache drivers, we'll use a different approach
                    Cache::flush();
                    break;
                }
            } catch (\Exception $e) {
                Log::warning("Failed to clear cache pattern {$pattern}: " . $e->getMessage());
            }
        }
    }

    /**
     * Clear cache for specific establishment
     */
    private function clearEstablishmentCache($estabelecimentoId)
    {
        // Get users from this establishment
        $userIds = \App\Models\User::where('estabelecimento_id', $estabelecimentoId)->pluck('id');
        
        foreach ($userIds as $userId) {
            $patterns = [
                "dashboard_stats_{$userId}",
                "evolution_data_{$userId}_*",
                "ambulance_data_{$userId}",
                "diagnosis_data_{$userId}",
                "realtime_stats_{$userId}"
            ];

            foreach ($patterns as $pattern) {
                try {
                    if (str_contains($pattern, '*')) {
                        if (config('cache.default') === 'redis') {
                            $keys = Cache::getRedis()->keys($pattern);
                            if (!empty($keys)) {
                                Cache::getRedis()->del($keys);
                            }
                        }
                    } else {
                        Cache::forget($pattern);
                    }
                } catch (\Exception $e) {
                    Log::warning("Failed to clear cache key {$pattern}: " . $e->getMessage());
                }
            }
        }
    }
}
