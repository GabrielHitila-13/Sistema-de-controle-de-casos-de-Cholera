<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Veiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AmbulanceController extends Controller
{
    /**
     * Listar ambulâncias
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        $query = Veiculo::with(['estabelecimento']);
        
        // Filtrar por estabelecimento se não for admin
        if (!$user->isAdmin()) {
            $query->where('estabelecimento_id', $user->estabelecimento_id);
        }

        // Filtros opcionais
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        $ambulancias = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $ambulancias
        ]);
    }

    /**
     * Criar nova ambulância
     */
    public function store(Request $request)
    {
        $user = $request->user();
        
        if (!$user->temAlgumPapel(['administrador', 'gestor', 'tecnico'])) {
            return response()->json([
                'success' => false,
                'message' => 'Acesso negado. Você não tem permissão para cadastrar ambulâncias.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'placa' => 'required|string|max:10|unique:veiculos',
            'modelo' => 'required|string|max:100',
            'ano' => 'required|integer|min:1990|max:' . (date('Y') + 1),
            'tipo' => 'required|in:ambulancia,suporte_basico,suporte_avancado,resgate',
            'estabelecimento_id' => 'required|exists:estabelecimentos,id',
            'equipamentos' => 'nullable|array',
            'observacoes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados de validação inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $ambulancia = Veiculo::create(array_merge(
            $request->validated(),
            ['status' => 'disponivel']
        ));

        return response()->json([
            'success' => true,
            'message' => 'Ambulância cadastrada com sucesso',
            'data' => $ambulancia->load(['estabelecimento'])
        ], 201);
    }

    /**
     * Exibir ambulância específica
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        
        $query = Veiculo::with(['estabelecimento']);
        
        // Filtrar por estabelecimento se não for admin
        if (!$user->isAdmin()) {
            $query->where('estabelecimento_id', $user->estabelecimento_id);
        }

        $ambulancia = $query->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $ambulancia
        ]);
    }

    /**
     * Atualizar ambulância
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();
        
        if (!$user->temAlgumPapel(['administrador', 'gestor', 'tecnico'])) {
            return response()->json([
                'success' => false,
                'message' => 'Acesso negado. Você não tem permissão para editar ambulâncias.'
            ], 403);
        }

        $query = Veiculo::query();
        
        // Filtrar por estabelecimento se não for admin
        if (!$user->isAdmin()) {
            $query->where('estabelecimento_id', $user->estabelecimento_id);
        }

        $ambulancia = $query->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'placa' => 'sometimes|string|max:10|unique:veiculos,placa,' . $id,
            'modelo' => 'sometimes|string|max:100',
            'ano' => 'sometimes|integer|min:1990|max:' . (date('Y') + 1),
            'tipo' => 'sometimes|in:ambulancia,suporte_basico,suporte_avancado,resgate',
            'status' => 'sometimes|in:disponivel,em_uso,manutencao,indisponivel',
            'equipamentos' => 'nullable|array',
            'observacoes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados de validação inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $ambulancia->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Ambulância atualizada com sucesso',
            'data' => $ambulancia->load(['estabelecimento'])
        ]);
    }

    /**
     * Atualizar status da ambulância
     */
    public function updateStatus(Request $request, $id)
    {
        $user = $request->user();
        
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:disponivel,em_uso,manutencao,indisponivel',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'observacoes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados de validação inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $query = Veiculo::query();
        
        // Filtrar por estabelecimento se não for admin
        if (!$user->isAdmin()) {
            $query->where('estabelecimento_id', $user->estabelecimento_id);
        }

        $ambulancia = $query->findOrFail($id);

        $updateData = ['status' => $request->status];
        
        if ($request->has('latitude') && $request->has('longitude')) {
            $updateData['ultima_localizacao'] = [
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'timestamp' => now()
            ];
        }

        if ($request->has('observacoes')) {
            $updateData['observacoes'] = $request->observacoes;
        }

        $ambulancia->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Status da ambulância atualizado com sucesso',
            'data' => $ambulancia
        ]);
    }

    /**
     * Excluir ambulância
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        
        if (!$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Acesso negado. Apenas administradores podem excluir ambulâncias.'
            ], 403);
        }

        $ambulancia = Veiculo::findOrFail($id);
        
        if ($ambulancia->status === 'em_uso') {
            return response()->json([
                'success' => false,
                'message' => 'Não é possível excluir ambulância em uso.'
            ], 422);
        }

        $ambulancia->delete();

        return response()->json([
            'success' => true,
            'message' => 'Ambulância excluída com sucesso'
        ]);
    }
}
