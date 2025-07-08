<?php

namespace App\Services;

use App\Models\Paciente;

class ColeraDetectionService
{
    private const SINTOMAS_COLERA = [
        'diarreia aquosa' => 25,
        'diarreia profusa' => 30,
        'diarreia líquida' => 25,
        'vômitos' => 15,
        'desidratação' => 20,
        'desidratação severa' => 25,
        'cãibras musculares' => 10,
        'fraqueza' => 5,
        'sede excessiva' => 8,
        'pele ressecada' => 10,
        'olhos fundos' => 12,
        'pulso fraco' => 15,
        'pressão baixa' => 15,
    ];

    private const FATORES_RISCO = [
        'contato_caso_confirmado' => 20,
        'area_surto' => 15,
        'agua_contaminada' => 15,
        'alimentos_contaminados' => 10,
        'saneamento_precario' => 10,
        'viagem_area_endemica' => 12,
    ];

    public function avaliarPaciente(Paciente $paciente): array
    {
        $pontuacao = 0;
        $sintomas_identificados = [];
        $fatores_risco = [];

        // Analisar sintomas
        $sintomas_texto = strtolower($paciente->sintomas);
        
        foreach (self::SINTOMAS_COLERA as $sintoma => $pontos) {
            if (strpos($sintomas_texto, $sintoma) !== false) {
                $pontuacao += $pontos;
                $sintomas_identificados[] = ucfirst($sintoma);
            }
        }

        // Analisar fatores de risco
        if ($paciente->contato_caso_confirmado) {
            $pontuacao += self::FATORES_RISCO['contato_caso_confirmado'];
            $fatores_risco[] = 'Contato com caso confirmado';
        }

        if ($paciente->area_surto) {
            $pontuacao += self::FATORES_RISCO['area_surto'];
            $fatores_risco[] = 'Área com surto ativo';
        }

        if ($paciente->agua_contaminada) {
            $pontuacao += self::FATORES_RISCO['agua_contaminada'];
            $fatores_risco[] = 'Exposição à água contaminada';
        }

        // Determinar diagnóstico baseado na pontuação
        $diagnostico = $this->determinarDiagnostico($pontuacao);
        $probabilidade = min(100, ($pontuacao / 100) * 100);

        return [
            'diagnostico' => $diagnostico,
            'probabilidade' => round($probabilidade, 2),
            'pontuacao' => $pontuacao,
            'sintomas_identificados' => implode(', ', $sintomas_identificados),
            'fatores_risco' => implode(', ', $fatores_risco),
            'recomendacoes' => $this->gerarRecomendacoes($diagnostico, $pontuacao),
        ];
    }

    private function determinarDiagnostico(int $pontuacao): string
    {
        if ($pontuacao >= 70) {
            return 'confirmado';
        } elseif ($pontuacao >= 50) {
            return 'provavel';
        } elseif ($pontuacao >= 25) {
            return 'suspeito';
        } elseif ($pontuacao > 0) {
            return 'pendente';
        } else {
            return 'descartado';
        }
    }

    private function gerarRecomendacoes(string $diagnostico, int $pontuacao): string
    {
        $recomendacoes = [];

        switch ($diagnostico) {
            case 'confirmado':
                $recomendacoes = [
                    'Isolamento imediato do paciente',
                    'Hidratação endovenosa urgente',
                    'Coleta de amostra para confirmação laboratorial',
                    'Notificação compulsória às autoridades sanitárias',
                    'Investigação epidemiológica dos contatos',
                    'Antibioticoterapia conforme protocolo',
                    'Monitoramento rigoroso dos sinais vitais'
                ];
                break;

            case 'provavel':
                $recomendacoes = [
                    'Isolamento preventivo',
                    'Hidratação oral ou endovenosa conforme necessário',
                    'Coleta de amostra para análise laboratorial',
                    'Monitoramento clínico frequente',
                    'Investigação de contatos próximos',
                    'Orientações sobre higiene e saneamento'
                ];
                break;

            case 'suspeito':
                $recomendacoes = [
                    'Observação clínica por 24-48 horas',
                    'Hidratação oral adequada',
                    'Orientações sobre sinais de alerta',
                    'Retorno se houver piora do quadro',
                    'Medidas de precaução básicas'
                ];
                break;

            case 'pendente':
                $recomendacoes = [
                    'Avaliação médica complementar',
                    'Investigação de outros diagnósticos diferenciais',
                    'Orientações gerais de saúde',
                    'Retorno programado em 24 horas'
                ];
                break;

            default:
                $recomendacoes = [
                    'Tratamento sintomático conforme necessário',
                    'Orientações gerais de saúde',
                    'Retorno se necessário'
                ];
        }

        return implode('; ', $recomendacoes);
    }

    public function getEstatisticas(): array
    {
        return [
            'total' => Paciente::count(),
            'confirmados' => Paciente::where('diagnostico_colera', 'confirmado')->count(),
            'provaveis' => Paciente::where('diagnostico_colera', 'provavel')->count(),
            'suspeitos' => Paciente::where('diagnostico_colera', 'suspeito')->count(),
            'descartados' => Paciente::where('diagnostico_colera', 'descartado')->count(),
            'pendentes' => Paciente::where('diagnostico_colera', 'pendente')->count(),
        ];
    }
}
