<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Estabelecimento;
use App\Services\GeolocationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HospitalController extends Controller
{
    protected $geolocationService;

    public function __construct(GeolocationService $geolocationService)
    {
        $this->geolocationService = $geolocationService;
    }

    /**
     * Listar hospitais
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        $query = Estabelecimento::with(['gabinete']);
        
        // Filtrar por gabinete se não for admin
        if (!$user->isAdmin() && $user->estabelecimento) {
            $query->where('gabinete_id', $user->estabelecimento->gabinete_id);
        }

        // Filtros opcionais
        if ($request->has('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nome', 'like', "%{$search}%")
                  ->orWhere('endereco', 'like', "%{$search}%");
            });
        }

        $hospitais = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $hospitais
        ]);
    }

    /**
     * Buscar hospitais próximos
     */
    public function nearby(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'nullable|integer|min:1|max:100',
            'tipo' => 'nullable|in:hospital,clinica,posto_saude,upa',
            'especialidade' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados de validação inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $hospitaisProximos = $this->geolocationService->buscarHospitaisProximos(
            $request->latitude,
            $request->longitude,
            $request->get('radius', 10),
            $request->tipo,
            $request->especialidade
        );

        return response()->json([
            'success' => true,
            'data' => $hospitaisProximos
        ]);
    }

    /**
     * Criar novo hospital
     */
    public function store(Request $request)
    {
        $user = $request->user();
        
        if (!$user->podeGerenciarEstabelecimentos()) {
            return response()->json([
                'success' => false,
                'message' => 'Acesso negado. Você não tem permissão para criar hospitais.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:255',
            'tipo' => 'required|in:hospital,clinica,posto_saude,upa',
            'endereco' => 'required|string|max:500',
            'telefone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'capacidade_leitos' => 'nullable|integer|min:0',
            'especialidades' => 'nullable|array',
            'gabinete_id' => 'required|exists:gabinetes,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados de validação inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $hospital = Estabelecimento::create(array_merge(
            $request->validated(),
            ['status' => 'ativo']
        ));

        return response()->json([
            'success' => true,
            'message' => 'Hospital cadastrado com sucesso',
            'data' => $hospital->load(['gabinete'])
        ], 201);
    }

    /**
     * Exibir hospital específico
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        
        $query = Estabelecimento::with(['gabinete', 'pacientes']);
        
        // Filtrar por gabinete se não for admin
        if (!$user->isAdmin() && $user->estabelecimento) {
            $query->where('gabinete_id', $user->estabelecimento->gabinete_id);
        }

        $hospital = $query->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $hospital
        ]);
    }

    /**
     * Atualizar hospital
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();
        
        if (!$user->podeGerenciarEstabelecimentos()) {
            return response()->json([
                'success' => false,
                'message' => 'Acesso negado. Você não tem permissão para editar hospitais.'
            ], 403);
        }

        $query = Estabelecimento::query();
        
        // Filtrar por gabinete se não for admin
        if (!$user->isAdmin() && $user->estabelecimento) {
            $query->where('gabinete_id', $user->estabelecimento->gabinete_id);
        }

        $hospital = $query->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nome' => 'sometimes|string|max:255',
            'tipo' => 'sometimes|in:hospital,clinica,posto_saude,upa',
            'endereco' => 'sometimes|string|max:500',
            'telefone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'capacidade_leitos' => 'nullable|integer|min:0',
            'especialidades' => 'nullable|array',
            'status' => 'sometimes|in:ativo,inativo,manutencao',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados de validação inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $hospital->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Hospital atualizado com sucesso',
            'data' => $hospital->load(['gabinete'])
        ]);
    }

    /**
     * Excluir hospital
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        
        if (!$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Acesso negado. Apenas administradores podem excluir hospitais.'
            ], 403);
        }

        $hospital = Estabelecimento::findOrFail($id);
        
        // Verificar se há pacientes vinculados
        if ($hospital->pacientes()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Não é possível excluir hospital com pacientes vinculados.'
            ], 422);
        }

        $hospital->delete();

        return response()->json([
            'success' => true,
            'message' => 'Hospital excluído com sucesso'
        ]);
    }
}
