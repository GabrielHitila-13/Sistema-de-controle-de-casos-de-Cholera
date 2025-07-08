<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\TriagemService;
use App\Services\GeolocationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TriageController extends Controller
{
    protected $triagemService;
    protected $geolocationService;

    public function __construct(TriagemService $triagemService, GeolocationService $geolocationService)
    {
        $this->triagemService = $triagemService;
        $this->geolocationService = $geolocationService;
    }

    /**
     * Triagem rápida (endpoint público)
     */
    public function triage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sintomas' => 'required|array|min:1',
            'sintomas.*' => 'string',
            'sintomas_outros' => 'nullable|string',
            'idade' => 'nullable|integer|min:0|max:120',
            'sexo' => 'nullable|in:M,F',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados de validação inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Realizar avaliação de risco
            $resultado = $this->triagemService->avaliarRisco(
                $request->input('sintomas'),
                $request->input('sintomas_outros', '')
            );

            // Buscar hospitais próximos se coordenadas fornecidas
            $hospitaisProximos = [];
            if ($request->has('latitude') && $request->has('longitude')) {
                $hospitaisProximos = $this->geolocationService->buscarHospitaisProximos(
                    $request->input('latitude'),
                    $request->input('longitude'),
                    5
                );
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'triagem' => $resultado,
                    'hospitais_proximos' => $hospitaisProximos,
                    'recomendacao_encaminhamento' => $this->getRecomendacaoEncaminhamento($resultado),
                    'instrucoes_emergencia' => $this->getInstrucoesEmergencia($resultado),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro na triagem: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obter recomendação de encaminhamento
     */
    private function getRecomendacaoEncaminhamento(array $resultado): array
    {
        $nivel = $resultado['nivel'];
        $urgencia = $resultado['urgencia'];

        if ($urgencia === 'emergencia') {
            return [
                'necessario' => true,
                'prioridade' => 'emergencia',
                'meio_transporte' => 'ambulancia',
                'tempo_maximo' => '15 minutos',
                'instrucoes' => 'Chamar ambulância IMEDIATAMENTE. Não transportar por meios próprios.'
            ];
        }

        if ($nivel === 'alto') {
            return [
                'necessario' => true,
                'prioridade' => 'urgente',
                'meio_transporte' => 'ambulancia_ou_transporte_rapido',
                'tempo_maximo' => '30 minutos',
                'instrucoes' => 'Encaminhar para hospital com urgência. Preferir ambulância.'
            ];
        }

        if ($nivel === 'medio') {
            return [
                'necessario' => true,
                'prioridade' => 'atencao',
                'meio_transporte' => 'qualquer',
                'tempo_maximo' => '2 horas',
                'instrucoes' => 'Procurar atendimento médico. Pode usar transporte próprio.'
            ];
        }

        return [
            'necessario' => false,
            'prioridade' => 'monitoramento',
            'meio_transporte' => null,
            'tempo_maximo' => null,
            'instrucoes' => 'Monitoramento domiciliar. Procurar ajuda se sintomas piorarem.'
        ];
    }

    /**
     * Obter instruções de emergência
     */
    private function getInstrucoesEmergencia(array $resultado): array
    {
        $instrucoes = [
            'hidratacao' => 'Manter hidratação constante com soro caseiro ou água limpa',
            'isolamento' => 'Manter isolamento para evitar contágio',
            'higiene' => 'Lavar as mãos frequentemente com água e sabão',
            'alimentacao' => 'Evitar alimentos sólidos até melhora dos sintomas'
        ];

        if ($resultado['nivel'] === 'alto' || $resultado['urgencia'] === 'emergencia') {
            $instrucoes['emergencia'] = 'Em caso de piora súbita: ligar 112 ou dirigir-se ao hospital mais próximo';
            $instrucoes['sinais_alerta'] = 'Sinais de alerta: desidratação severa, vômitos persistentes, prostração';
        }

        return $instrucoes;
    }
}
