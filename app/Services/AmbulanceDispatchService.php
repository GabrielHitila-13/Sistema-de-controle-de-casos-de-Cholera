<?php

namespace App\Services;

use App\Models\Paciente;
use App\Models\Veiculo;
use App\Models\Estabelecimento;
use App\Services\GeolocationService;
use App\Services\TriagemService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class AmbulanceDispatchService
{
    protected $geolocationService;
    protected $triagemService;

    public function __construct(GeolocationService $geolocationService, TriagemService $triagemService)
    {
        $this->geolocationService = $geolocationService;
        $this->triagemService = $triagemService;
    }

    /**
     * Redirecionamento inteligente de ambulância
     */
    public function dispatchAmbulance(array $dadosPaciente, array $sintomas, ?float $latitude = null, ?float $longitude = null): array
    {
        DB::beginTransaction();
        
        try {
            // 1. Criar registro do paciente
            $paciente = $this->criarPaciente($dadosPaciente);
            
            // 2. Realizar triagem automatizada
            $resultadoTriagem = $this->triagemService->avaliarRisco($sintomas, $dadosPaciente['sintomas_outros'] ?? '');
            
            // 3. Atualizar paciente com resultado da triagem
            $paciente->update([
                'risco' => $resultadoTriagem['nivel'],
                'prioridade' => $this->mapearPrioridadeTriagem($resultadoTriagem['nivel']),
                'sintomas' => json_encode($sintomas),
                'resultado_triagem' => json_encode($resultadoTriagem),
                'data_triagem' => now(),
            ]);

            // 4. Verificar se precisa de encaminhamento
            if (!$this->precisaEncaminhamento($resultadoTriagem)) {
                DB::commit();
                return [
                    'paciente' => $paciente,
                    'triagem' => $resultadoTriagem,
                    'encaminhamento' => false,
                    'recomendacao' => 'Monitoramento domiciliar'
                ];
            }

            // 5. Buscar hospital mais adequado
            $hospitalSelecionado = $this->buscarHospitalMaisAdequado($latitude, $longitude, $resultadoTriagem['nivel']);
            
            if (!$hospitalSelecionado) {
                throw new \Exception('Nenhum hospital disponível encontrado na região');
            }

            // 6. Buscar ambulância disponível
            $ambulanciaSelecionada = $this->buscarAmbulanciaDisponivel($hospitalSelecionado, $latitude, $longitude);
            
            if (!$ambulanciaSelecionada) {
                throw new \Exception('Nenhuma ambulância disponível encontrada');
            }

            // 7. Criar missão de ambulância
            $missao = $this->criarMissaoAmbulancia($paciente, $ambulanciaSelecionada, $hospitalSelecionado, $latitude, $longitude);

            // 8. Gerar QR Code do paciente
            $this->triagemService->gerarQrCode($paciente);

            // 9. Obter rota otimizada
            $rota = $this->geolocationService->obterRota($latitude, $longitude, $hospitalSelecionado);

            DB::commit();

            // 10. Notificar equipes (implementar posteriormente)
            $this->notificarEquipes($paciente, $ambulanciaSelecionada, $hospitalSelecionado);

            return [
                'paciente' => $paciente->fresh(),
                'triagem' => $resultadoTriagem,
                'hospital' => $hospitalSelecionado,
                'ambulancia' => $ambulanciaSelecionada->fresh(),
                'missao' => $missao,
                'rota' => $rota,
                'encaminhamento' => true,
                'tempo_estimado' => $this->calcularTempoEstimado($latitude, $longitude, $hospitalSelecionado),
                'qr_code' => $paciente->qr_code,
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro no redirecionamento de ambulância: ' . $e->getMessage(), [
                'dados_paciente' => $dadosPaciente,
                'sintomas' => $sintomas,
                'coordenadas' => [$latitude, $longitude]
            ]);
            
            throw $e;
        }
    }

    /**
     * Criar registro do paciente
     */
    private function criarPaciente(array $dados): Paciente
    {
        return Paciente::create([
            'nome' => $dados['nome'],
            'data_nascimento' => $dados['data_nascimento'],
            'sexo' => $dados['sexo'],
            'telefone' => $dados['telefone'] ?? null,
            'endereco' => $dados['endereco'] ?? null,
            'bi' => $dados['bi'] ?? null,
            'status' => 'aguardando',
            'estabelecimento_id' => $dados['estabelecimento_id'] ?? null,
        ]);
    }

    /**
     * Mapear nível de risco para prioridade
     */
    private function mapearPrioridadeTriagem(string $nivelRisco): string
    {
        return match($nivelRisco) {
            'alto' => 'critica',
            'medio' => 'alta',
            'baixo' => 'media',
            default => 'baixa'
        };
    }

    /**
     * Verificar se precisa de encaminhamento
     */
    private function precisaEncaminhamento(array $resultadoTriagem): bool
    {
        return in_array($resultadoTriagem['nivel'], ['alto', 'medio']) || 
               $resultadoTriagem['urgencia'] === 'emergencia';
    }

    /**
     * Buscar hospital mais adequado
     */
    private function buscarHospitalMaisAdequado(?float $latitude, ?float $longitude, string $nivelRisco): ?Estabelecimento
    {
        $query = Estabelecimento::where('status', 'ativo')
            ->whereIn('categoria', $this->getCategoriasPorRisco($nivelRisco))
            ->where('capacidade', '>', 0);

        // Se temos coordenadas, buscar por proximidade
        if ($latitude && $longitude) {
            $hospitaisProximos = $this->geolocationService->buscarHospitaisProximos($latitude, $longitude, 5);
            
            if (!empty($hospitaisProximos)) {
                $idsProximos = collect($hospitaisProximos)->pluck('id')->toArray();
                $query->whereIn('id', $idsProximos);
            }
        }

        // Ordenar por capacidade disponível e priorizar por tipo
        return $query->orderByRaw("
            CASE categoria 
                WHEN 'centro' THEN 1 
                WHEN 'geral' THEN 2 
                WHEN 'municipal' THEN 3 
                ELSE 4 
            END
        ")
        ->orderBy('capacidade', 'desc')
        ->first();
    }

    /**
     * Obter categorias de hospital por nível de risco
     */
    private function getCategoriasPorRisco(string $nivelRisco): array
    {
        return match($nivelRisco) {
            'alto' => ['centro', 'geral'],
            'medio' => ['geral', 'municipal', 'centro'],
            'baixo' => ['municipal', 'geral'],
            default => ['municipal']
        };
    }

    /**
     * Buscar ambulância disponível
     */
    private function buscarAmbulanciaDisponivel(Estabelecimento $hospital, ?float $latitude, ?float $longitude): ?Veiculo
    {
        $query = Veiculo::where('status', 'disponivel')
            ->whereIn('tipo', ['ambulancia', 'suporte_basico', 'suporte_avancado']);

        // Priorizar ambulâncias do mesmo estabelecimento
        $ambulanciaLocal = (clone $query)->where('estabelecimento_id', $hospital->id)->first();
        
        if ($ambulanciaLocal) {
            return $ambulanciaLocal;
        }

        // Buscar ambulâncias próximas se temos coordenadas
        if ($latitude && $longitude) {
            // Implementar busca por proximidade de ambulâncias
            // Por enquanto, buscar qualquer ambulância disponível
        }

        return $query->orderBy('tipo', 'desc') // Priorizar suporte avançado
            ->first();
    }

    /**
     * Criar missão de ambulância
     */
    private function criarMissaoAmbulancia(Paciente $paciente, Veiculo $ambulancia, Estabelecimento $hospital, ?float $latitude, ?float $longitude): array
    {
        // Atualizar status da ambulância
        $ambulancia->update([
            'status' => 'em_uso',
            'missao_atual' => [
                'paciente_id' => $paciente->id,
                'hospital_destino_id' => $hospital->id,
                'coordenadas_origem' => $latitude && $longitude ? [$latitude, $longitude] : null,
                'inicio_missao' => now(),
                'status' => 'a_caminho_paciente'
            ]
        ]);

        // Atualizar paciente com ambulância designada
        $paciente->update([
            'veiculo_id' => $ambulancia->id,
            'hospital_destino_id' => $hospital->id,
            'status' => 'ambulancia_designada'
        ]);

        return [
            'id' => uniqid('missao_'),
            'paciente_id' => $paciente->id,
            'ambulancia_id' => $ambulancia->id,
            'hospital_id' => $hospital->id,
            'status' => 'ativa',
            'criada_em' => now(),
            'coordenadas_origem' => $latitude && $longitude ? [$latitude, $longitude] : null,
        ];
    }

    /**
     * Calcular tempo estimado
     */
    private function calcularTempoEstimado(?float $latitude, ?float $longitude, Estabelecimento $hospital): ?string
    {
        if (!$latitude || !$longitude || !$hospital->latitude || !$hospital->longitude) {
            return null;
        }

        $distancia = $this->geolocationService->calcularDistancia(
            $latitude, $longitude, 
            $hospital->latitude, $hospital->longitude
        );

        // Velocidade média urbana: 30 km/h
        $tempoHoras = $distancia / 30;
        $tempoMinutos = $tempoHoras * 60;

        if ($tempoMinutos < 60) {
            return round($tempoMinutos) . ' minutos';
        } else {
            $horas = floor($tempoMinutos / 60);
            $minutos = round($tempoMinutos % 60);
            return $horas . 'h ' . $minutos . 'min';
        }
    }

    /**
     * Notificar equipes
     */
    private function notificarEquipes(Paciente $paciente, Veiculo $ambulancia, Estabelecimento $hospital): void
    {
        // Implementar notificações em tempo real
        // - WebSocket para condutor da ambulância
        // - SMS/WhatsApp para equipe médica
        // - Notificação push para app móvel
        
        Log::info('Ambulância despachada', [
            'paciente_id' => $paciente->id,
            'ambulancia_id' => $ambulancia->id,
            'hospital_id' => $hospital->id,
            'prioridade' => $paciente->prioridade
        ]);
    }

    /**
     * Obter status de todas as ambulâncias
     */
    public function getAmbulanceStatus(): array
    {
        return Cache::remember('ambulance_status', 60, function() {
            return Veiculo::with(['estabelecimento'])
                ->select(['id', 'placa', 'tipo', 'status', 'estabelecimento_id', 'missao_atual', 'ultima_localizacao'])
                ->get()
                ->groupBy('status')
                ->toArray();
        });
    }

    /**
     * Atualizar localização da ambulância
     */
    public function updateAmbulanceLocation(int $ambulanciaId, float $latitude, float $longitude): bool
    {
        $ambulancia = Veiculo::find($ambulanciaId);
        
        if (!$ambulancia) {
            return false;
        }

        $ambulancia->update([
            'ultima_localizacao' => [
                'latitude' => $latitude,
                'longitude' => $longitude,
                'timestamp' => now()
            ]
        ]);

        // Limpar cache
        Cache::forget('ambulance_status');

        return true;
    }
}
