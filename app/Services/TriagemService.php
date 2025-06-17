<?php

namespace App\Services;

use App\Models\Paciente;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TriagemService
{
    private $sintomasRisco = [
        'alto' => [
            'diarreia aquosa' => 4,
            'diarreia abundante' => 4,
            'desidratacao severa' => 5,
            'desidratacao grave' => 5,
            'vomito intenso' => 3,
            'prostração' => 3,
            'choque' => 5,
            'hipotensao' => 4,
        ],
        'medio' => [
            'diarreia' => 2,
            'vomito' => 2,
            'desidratacao' => 3,
            'desidratacao moderada' => 3,
            'dor abdominal intensa' => 2,
            'febre alta' => 2,
            'fraqueza intensa' => 2,
        ],
        'baixo' => [
            'febre' => 1,
            'febre baixa' => 1,
            'dor abdominal' => 1,
            'dor abdominal leve' => 1,
            'fraqueza' => 1,
            'mal-estar' => 1,
            'nauseas' => 1,
            'dor de cabeça' => 1,
        ]
    ];

    public function avaliarRisco(array $sintomas, string $sintomasOutros = ''): array
    {
        $pontuacao = 0;
        $sintomasDetectados = [];
        $recomendacoes = [];

        // Avaliar sintomas selecionados
        foreach ($sintomas as $sintoma) {
            $pontos = $this->calcularPontosSintoma($sintoma);
            $pontuacao += $pontos;
            $sintomasDetectados[] = $sintoma;
        }

        // Avaliar sintomas em texto livre
        if (!empty($sintomasOutros)) {
            $pontosTexto = $this->analisarTextoSintomas($sintomasOutros);
            $pontuacao += $pontosTexto;
        }

        // Determinar nível de risco
        $nivel = $this->determinarNivelRisco($pontuacao);
        
        // Gerar recomendações
        $recomendacoes = $this->gerarRecomendacoes($nivel, $pontuacao);

        return [
            'pontuacao' => $pontuacao,
            'nivel' => $nivel,
            'sintomas_detectados' => $sintomasDetectados,
            'recomendacoes' => $recomendacoes,
            'urgencia' => $this->determinarUrgencia($nivel, $pontuacao),
            'cor' => $this->obterCorRisco($nivel),
            'icone' => $this->obterIconeRisco($nivel),
        ];
    }

    private function calcularPontosSintoma(string $sintoma): int
    {
        $sintoma = strtolower($sintoma);
        
        foreach ($this->sintomasRisco as $categoria => $sintomas) {
            foreach ($sintomas as $sintomaKey => $pontos) {
                if (str_contains($sintoma, $sintomaKey)) {
                    return $pontos;
                }
            }
        }
        
        return 0;
    }

    private function analisarTextoSintomas(string $texto): int
    {
        $texto = strtolower($texto);
        $pontuacao = 0;
        
        foreach ($this->sintomasRisco as $categoria => $sintomas) {
            foreach ($sintomas as $sintoma => $pontos) {
                if (str_contains($texto, $sintoma)) {
                    $pontuacao += $pontos;
                }
            }
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
