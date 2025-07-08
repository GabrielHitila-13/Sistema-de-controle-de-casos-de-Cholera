<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Paciente;
use App\Services\TriagemService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PatientController extends Controller
{
    protected $triagemService;

    public function __construct(TriagemService $triagemService)
    {
        $this->triagemService = $triagemService;
    }

    /**
     * Listar pacientes
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        $query = Paciente::with(['estabelecimento', 'pontoAtendimento']);
        
        // Filtrar por estabelecimento se não for admin
        if (!$user->isAdmin()) {
            $query->where('estabelecimento_id', $user->estabelecimento_id);
        }

        // Filtros opcionais
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('prioridade')) {
            $query->where('prioridade', $request->prioridade);
        }

        if ($request->has('data_inicio')) {
            $query->whereDate('created_at', '>=', $request->data_inicio);
        }

        if ($request->has('data_fim')) {
            $query->whereDate('created_at', '<=', $request->data_fim);
        }

        $pacientes = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $pacientes
        ]);
    }

    /**
     * Criar novo paciente
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:255',
            'data_nascimento' => 'required|date',
            'sexo' => 'required|in:M,F',
            'telefone' => 'nullable|string|max:20',
            'endereco' => 'nullable|string|max:500',
            'estabelecimento_id' => 'required|exists:estabelecimentos,id',
            'sintomas' => 'nullable|string',
            'observacoes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados de validação inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $paciente = Paciente::create(array_merge(
            $request->validated(),
            ['status' => 'aguardando']
        ));

        return response()->json([
            'success' => true,
            'message' => 'Paciente cadastrado com sucesso',
            'data' => $paciente->load(['estabelecimento'])
        ], 201);
    }

    /**
     * Exibir paciente específico
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        
        $query = Paciente::with(['estabelecimento', 'pontoAtendimento']);
        
        // Filtrar por estabelecimento se não for admin
        if (!$user->isAdmin()) {
            $query->where('estabelecimento_id', $user->estabelecimento_id);
        }

        $paciente = $query->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $paciente
        ]);
    }

    /**
     * Atualizar paciente
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();
        
        $query = Paciente::query();
        
        // Filtrar por estabelecimento se não for admin
        if (!$user->isAdmin()) {
            $query->where('estabelecimento_id', $user->estabelecimento_id);
        }

        $paciente = $query->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nome' => 'sometimes|string|max:255',
            'data_nascimento' => 'sometimes|date',
            'sexo' => 'sometimes|in:M,F',
            'telefone' => 'nullable|string|max:20',
            'endereco' => 'nullable|string|max:500',
            'sintomas' => 'nullable|string',
            'observacoes' => 'nullable|string',
            'status' => 'sometimes|in:aguardando,em_atendimento,finalizado,transferido',
            'prioridade' => 'sometimes|in:baixa,media,alta,critica',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados de validação inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $paciente->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Paciente atualizado com sucesso',
            'data' => $paciente->load(['estabelecimento'])
        ]);
    }

    /**
     * Realizar triagem do paciente
     */
    public function triage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'paciente_id' => 'required|exists:pacientes,id',
            'sintomas' => 'required|array',
            'sinais_vitais' => 'required|array',
            'sinais_vitais.pressao_arterial' => 'nullable|string',
            'sinais_vitais.frequencia_cardiaca' => 'nullable|integer',
            'sinais_vitais.temperatura' => 'nullable|numeric',
            'sinais_vitais.saturacao_oxigenio' => 'nullable|integer',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados de validação inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $resultado = $this->triagemService->realizarTriagem(
            $request->paciente_id,
            $request->sintomas,
            $request->sinais_vitais,
            $request->latitude,
            $request->longitude
        );

        return response()->json([
            'success' => true,
            'message' => 'Triagem realizada com sucesso',
            'data' => $resultado
        ]);
    }

    /**
     * Excluir paciente
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        
        if (!$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Acesso negado. Apenas administradores podem excluir pacientes.'
            ], 403);
        }

        $paciente = Paciente::findOrFail($id);
        $paciente->delete();

        return response()->json([
            'success' => true,
            'message' => 'Paciente excluído com sucesso'
        ]);
    }
}
