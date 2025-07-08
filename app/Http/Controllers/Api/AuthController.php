<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    /**
     * Login do usuário
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados de validação inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciais inválidas'
            ], 401);
        }

        $user = Auth::user();
        
        // Verificar se o usuário está ativo
        if (!$user->ativo) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário inativo. Entre em contato com o administrador.'
            ], 403);
        }

        // Definir abilities baseadas no papel do usuário
        $abilities = $this->getUserAbilities($user);
        
        // Criar token com abilities específicas
        $token = $user->createToken('api-token', $abilities)->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login realizado com sucesso',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'papel' => $user->papel,
                    'estabelecimento' => $user->estabelecimento ? [
                        'id' => $user->estabelecimento->id,
                        'nome' => $user->estabelecimento->nome,
                        'tipo' => $user->estabelecimento->categoria
                    ] : null
                ],
                'token' => $token,
                'token_type' => 'Bearer',
                'abilities' => $abilities
            ]
        ]);
    }

    /**
     * Registro de novo usuário (apenas admin)
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'papel' => 'required|in:administrador,gestor,medico,tecnico,enfermeiro,condutor,visualizacao',
            'estabelecimento_id' => 'nullable|exists:estabelecimentos,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados de validação inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'papel' => $request->papel,
            'estabelecimento_id' => $request->estabelecimento_id,
            'ativo' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Usuário criado com sucesso',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'papel' => $user->papel,
            ]
        ], 201);
    }

    /**
     * Logout do usuário
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout realizado com sucesso'
        ]);
    }

    /**
     * Dados do usuário atual
     */
    public function me(Request $request)
    {
        $user = $request->user();
        
        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'papel' => $user->papel,
                'estabelecimento' => $user->estabelecimento,
                'abilities' => $user->currentAccessToken()->abilities ?? []
            ]
        ]);
    }

    /**
     * Refresh token
     */
    public function refresh(Request $request)
    {
        $user = $request->user();
        
        // Revogar token atual
        $request->user()->currentAccessToken()->delete();
        
        // Criar novo token
        $abilities = $this->getUserAbilities($user);
        $token = $user->createToken('api-token', $abilities)->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Token renovado com sucesso',
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
                'abilities' => $abilities
            ]
        ]);
    }

    /**
     * Listar usuários (admin/gestor)
     */
    public function users(Request $request)
    {
        $user = $request->user();
        
        $query = User::with(['estabelecimento']);
        
        // Filtrar por estabelecimento se não for admin
        if ($user->papel !== 'administrador' && $user->estabelecimento) {
            $query->where('estabelecimento_id', $user->estabelecimento_id);
        }

        $users = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    /**
     * Definir abilities baseadas no papel do usuário
     */
    private function getUserAbilities(User $user): array
    {
        $abilities = [];

        switch ($user->papel) {
            case 'administrador':
                $abilities = [
                    'view-pacientes', 'create-pacientes', 'edit-pacientes', 'delete-pacientes',
                    'view-estabelecimentos', 'manage-estabelecimentos',
                    'view-ambulances', 'manage-veiculos', 'update-ambulance-status',
                    'view-relatorios', 'manage-users', 'view-all-data'
                ];
                break;

            case 'gestor':
                $abilities = [
                    'view-pacientes', 'create-pacientes', 'edit-pacientes',
                    'view-estabelecimentos', 'manage-estabelecimentos',
                    'view-ambulances', 'manage-veiculos', 'update-ambulance-status',
                    'view-relatorios'
                ];
                break;

            case 'medico':
                $abilities = [
                    'view-pacientes', 'edit-pacientes',
                    'view-estabelecimentos',
                    'view-ambulances',
                    'view-relatorios'
                ];
                break;

            case 'tecnico':
                $abilities = [
                    'view-pacientes', 'create-pacientes', 'edit-pacientes',
                    'view-estabelecimentos',
                    'view-ambulances', 'update-ambulance-status'
                ];
                break;

            case 'enfermeiro':
                $abilities = [
                    'view-pacientes', 'edit-pacientes',
                    'view-estabelecimentos',
                    'view-ambulances'
                ];
                break;

            case 'condutor':
                $abilities = [
                    'view-pacientes',
                    'view-estabelecimentos',
                    'view-ambulances', 'update-ambulance-status'
                ];
                break;

            case 'visualizacao':
                $abilities = [
                    'view-pacientes',
                    'view-estabelecimentos',
                    'view-ambulances',
                    'view-relatorios'
                ];
                break;
        }

        return $abilities;
    }
}
