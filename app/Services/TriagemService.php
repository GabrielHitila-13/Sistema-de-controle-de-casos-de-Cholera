<?php

namespace App\Services;

use App\Models\Paciente;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TriagemService
{
    protected $coleraDetectionService;

    public function __construct(ColeraDetectionService $coleraDetectionService)
    {
        $this->coleraDetectionService = $coleraDetectionService;
    }

    public function realizarTriagem(int $pacienteId, array $sintomas, array $sinaisVitais = [], array $fatoresRisco = []): array
    {
        $paciente = Paciente::findOrFail($pacienteId);
        
        // Avaliar probabilidade de cólera
        $avaliacaoColera = $this->coleraDetectionService->avaliarProbabilidadeColera(
            $sintomas,
            $fatoresRisco,
            [
                'data_nascimento' => $paciente->data_nascimento,
                'sexo' => $paciente->sexo
            ]
        );
        
        // Avaliar risco geral
        $avaliacaoRisco = $this->avaliarRisco($sintomas, $sinaisVitais);
        
        // Atualizar paciente com os resultados
        $paciente->update([
            'sintomas' => json_encode($sintomas),
            'sinais_vitais' => json_encode($sinaisVitais),
            'risco' => $avaliacaoRisco['nivel'],
            'diagnostico_colera' => $avaliacaoColera['diagnostico'],
            'probabilidade_colera' => $avaliacaoColera['probabilidade'],
            'sintomas_colera' => json_encode($avaliacaoColera['sintomas_detectados']),
            'fatores_risco' => json_encode($avaliacaoColera['fatores_risco_detectados']),
            'data_triagem' => now(),
            'data_diagnostico' => now(),
            'status' => $this->determinarStatusPaciente($avaliacaoColera['urgencia'])
        ]);
        
        // Gerar QR Code
        $this->gerarQrCode($paciente);
        
        return [
            'paciente' => $paciente->fresh(),
            'avaliacao_colera' => $avaliacaoColera,
            'avaliacao_risco' => $avaliacaoRisco,
            'requer_ambulancia' => $this->requerAmbulancia($avaliacaoColera['urgencia']),
            'hospital_recomendado' => $this->recomendarHospital($paciente, $avaliacaoColera['diagnostico']),
        ];
    }

    private function avaliarRisco(array $sintomas, array $sinaisVitais): array
    {
        $pontuacao = 0;
        $sintomasDetectados = [];
        
        // Avaliar sintomas
        foreach ($sintomas as $sintoma) {
            $pontos = $this->calcularPontosSintoma($sintoma);
            $pontuacao += $pontos;
            if ($pontos > 0) {
                $sintomasDetectados[] = $sintoma;
            }
        }
        
        // Avaliar sinais vitais
        $pontuacao += $this->avaliarSinaisVitais($sinaisVitais);
        
        $nivel = $this->determinarNivelRisco($pontuacao);
        $urgencia = $this->determinarUrgencia($nivel, $pontuacao);
        
        return [
            'pontuacao' => $pontuacao,
            'nivel' => $nivel,
            'urgencia' => $urgencia,
            'sintomas_detectados' => $sintomasDetectados,
            'recomendacoes' => $this->gerarRecomendacoes($nivel, $pontuacao),
            'cor' => $this->obterCorRisco($nivel),
            'icone' => $this->obterIconeRisco($nivel),
        ];
    }

    private function calcularPontosSintoma(string $sintoma): int
    {
        $sintoma = strtolower($sintoma);
        
        $sintomasRisco = [
            'diarreia aquosa' => 4,
            'diarreia abundante' => 4,
            'desidratacao severa' => 5,
            'desidratacao grave' => 5,
            'vomito intenso' => 3,
            'prostração' => 3,
            'choque' => 5,
            'hipotensao' => 4,
            'diarreia' => 2,
            'vomito' => 2,
            'desidratacao' => 3,
            'dor abdominal intensa' => 2,
            'febre alta' => 2,
            'fraqueza intensa' => 2,
            'febre' => 1,
            'dor abdominal' => 1,
            'fraqueza' => 1,
            'mal-estar' => 1,
            'nauseas' => 1,
            'dor de cabeça' => 1,
        ];
        
        foreach ($sintomasRisco as $sintomaKey => $pontos) {
            if (str_contains($sintoma, $sintomaKey)) {
                return $pontos;
            }
        }
        
        return 0;
    }

    private function avaliarSinaisVitais(array $sinaisVitais): int
    {
        $pontuacao = 0;
        
        // Temperatura
        if (isset($sinaisVitais['temperatura'])) {
            $temp = floatval($sinaisVitais['temperatura']);
            if ($temp >= 39) $pontuacao += 2;
            elseif ($temp >= 38) $pontuacao += 1;
            elseif ($temp <= 35) $pontuacao += 3;
        }
        
        // Frequência cardíaca
        if (isset($sinaisVitais['frequencia_cardiaca'])) {
            $fc = intval($sinaisVitais['frequencia_cardiaca']);
            if ($fc >= 120) $pontuacao += 2;
            elseif ($fc <= 50) $pontuacao += 2;
        }
        
        // Saturação de oxigênio
        if (isset($sinaisVitais['saturacao_oxigenio'])) {
            $sat = intval($sinaisVitais['saturacao_oxigenio']);
            if ($sat <= 90) $pontuacao += 3;
            elseif ($sat <= 95) $pontuacao += 1;
        }
        
        return $pontuacao;
    }

    private function determinarNivelRisco(int $pontuacao): string
    {
        if ($pontuacao >= 8) return 'alto';
        if ($pontuacao >= 4) return 'medio';
        return 'baixo';
    }

    private function determinarUrgencia(string $nivel, int $pontuacao): string
    {
        switch ($nivel) {
            case 'alto':
                return $pontuacao >= 10 ? 'emergencia' : 'urgente';
            case 'medio':
                return 'atencao';
            default:
                return 'monitoramento';
        }
    }

    private function determinarStatusPaciente(string $urgencia): string
    {
        return match($urgencia) {
            'emergencia', 'urgente' => 'aguardando',
            'atencao' => 'aguardando',
            default => 'aguardando'
        };
    }

    private function requerAmbulancia(string $urgencia): bool
    {
        return in_array($urgencia, ['emergencia', 'urgente']);
    }

    private function recomendarHospital($paciente, string $diagnostico): ?array
    {
        // Lógica para recomendar hospital baseado no diagnóstico e localização
        // Por enquanto retorna null, mas pode ser implementada
        return null;
    }

    private function gerarRecomendacoes(string $nivel, int $pontuacao): array
    {
        $recomendacoes = [];
        
        switch ($nivel) {
            case 'alto':
                $recomendacoes = [
                    'URGENTE: Encaminhar imediatamente para hospital',
                    'Iniciar hidratação oral ou endovenosa',
                    'Monitoramento contínuo dos sinais vitais',
                    'Isolamento do paciente',
                    'Notificação às autoridades de saúde',
                ];
                if ($pontuacao >= 10) {
                    array_unshift($recomendacoes, 'EMERGÊNCIA: Chamar ambulância imediatamente');
                }
                break;
                
            case 'medio':
                $recomendacoes = [
                    'Encaminhar para avaliação médica',
                    'Iniciar hidratação oral',
                    'Monitoramento de sintomas',
                    'Orientações sobre higiene',
                    'Retorno em 24h se piora',
                ];
                break;
                
            default:
                $recomendacoes = [
                    'Monitoramento domiciliar',
                    'Hidratação oral abundante',
                    'Orientações sobre prevenção',
                    'Retorno se surgir febre ou diarreia',
                    'Manter isolamento preventivo',
                ];
        }
        
        return $recomendacoes;
    }

    private function obterCorRisco(string $nivel): string
    {
        return match($nivel) {
            'alto' => 'red',
            'medio' => 'yellow',
            'baixo' => 'green',
            default => 'gray'
        };
    }

    private function obterIconeRisco(string $nivel): string
    {
        return match($nivel) {
            'alto' => 'fas fa-exclamation-triangle',
            'medio' => 'fas fa-exclamation-circle',
            'baixo' => 'fas fa-info-circle',
            default => 'fas fa-question-circle'
        };
    }

    public function gerarQrCode(Paciente $paciente): void
    {
        $dadosQr = [
            'id' => $paciente->id,
            'nome' => $paciente->nome,
            'bi' => $paciente->bi,
            'telefone' => $paciente->telefone,
            'risco' => $paciente->risco,
            'diagnostico_colera' => $paciente->diagnostico_colera,
            'probabilidade_colera' => $paciente->probabilidade_colera,
            'data_triagem' => $paciente->data_triagem?->format('Y-m-d H:i:s'),
            'estabelecimento' => $paciente->estabelecimento->nome ?? null,
            'sistema' => 'SGSC Angola',
            'url_verificacao' => route('pacientes.show', $paciente->id),
        ];

        try {
            $qrCode = QrCode::size(300)
                ->margin(2)
                ->errorCorrection('M')
                ->generate(json_encode($dadosQr));
                
            $paciente->update(['qr_code' => base64_encode($qrCode)]);
        } catch (\Exception $e) {
            \Log::error('Erro ao gerar QR Code: ' . $e->getMessage());
        }
    }
}
