<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AmbulanceDispatchService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DispatchController extends Controller
{
    protected $dispatchService;

    public function __construct(AmbulanceDispatchService $dispatchService)
    {
        $this->dispatchService = $dispatchService;
    }

    /**
     * Redirecionamento automático de ambulância
     */
    public function dispatch(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // Dados do paciente
            'paciente.nome' => 'required|string|max:255',
            'paciente.data_nascimento' => 'required|date',
            'paciente.sexo' => 'required|in:M,F',
            'paciente.telefone' => 'nullable|string|max:20',
            'paciente.endereco' => 'nullable|string|max:500',
            'paciente.bi' => 'nullable|string|max:20',
            'paciente.estabelecimento_id' => 'nullable|exists:estabelecimentos,id',
            
            // Sintomas para triagem
            'sintomas' => 'required|array|min:1',
            'sintomas.*' => 'string',
            'sintomas_outros' => 'nullable|string',
            
            // Localização
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            
            // Opções
            'prioridade_manual' => 'nullable|in:baixa,media,alta,critica',
            'hospital_preferido_id' => 'nullable|exists:estabelecimentos,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados de validação inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $resultado = $this->dispatchService->dispatchAmbulance(
                $request->input('paciente'),
                $request->input('sintomas'),
                $request->input('latitude'),
                $request->input('longitude')
            );

            $statusCode = $resultado['encaminhamento'] ? 201 : 200;
            $message = $resultado['encaminhamento'] 
                ? 'Ambulância despachada com sucesso' 
                : 'Triagem realizada - Monitoramento recomendado';

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $resultado
            ], $statusCode);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro no redirecionamento: ' . $e->getMessage(),
                'error_code' => 'DISPATCH_ERROR'
            ], 500);
        }
    }

    /**
     * Status de todas as ambulâncias
     */
    public function ambulanceStatus(Request $request)
    {
        try {
            $status = $this->dispatchService->getAmbulanceStatus();

            return response()->json([
                'success' => true,
                'data' => [
                    'status_resumo' => [
                        'disponivel' => count($status['disponivel'] ?? []),
                        'em_uso' => count($status['em_uso'] ?? []),
                        'manutencao' => count($status['manutencao'] ?? []),
                        'indisponivel' => count($status['indisponivel'] ?? []),
                    ],
                    'ambulancias' => $status,
                    'ultima_atualizacao' => now()
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao obter status das ambulâncias: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Atualizar localização da ambulância
     */
    public function updateLocation(Request $request, $ambulanciaId)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Coordenadas inválidas',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $success = $this->dispatchService->updateAmbulanceLocation(
                $ambulanciaId,
                $request->input('latitude'),
                $request->input('longitude')
            );

            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ambulância não encontrada'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Localização atualizada com sucesso'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar localização: ' . $e->getMessage()
            ], 500);
        }
    }
}
