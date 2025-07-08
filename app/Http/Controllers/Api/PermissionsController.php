<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PermissionsController extends Controller
{
    /**
     * Retorna as permissões detalhadas do usuário autenticado
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        $permissions = [
            'user_info' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'papel' => $user->papel,
                'estabelecimento_id' => $user->estabelecimento_id,
            ],
            'role_permissions' => $this->getRolePermissions($user->papel),
            'specific_permissions' => [
                'can_manage_users' => $user->podeGerenciarUsuarios(),
                'can_view_patients' => $user->podeVerPacientes(),
                'can_manage_establishments' => $user->podeGerenciarEstabelecimentos(),
                'is_admin' => $user->isAdmin(),
            ],
            'api_endpoints' => $this->getAvailableEndpoints($user->papel)
        ];

        return response()->json([
            'success' => true,
            'data' => $permissions
        ]);
    }

    /**
     * Obter permissões por papel
     */
    private function getRolePermissions(string $papel): array
    {
        $permissions = [
            'administrador' => [
                'description' => 'Acesso total ao sistema',
                'can_do' => [
                    'Cadastrar, editar e excluir hospitais',
                    'Gerenciar ambulâncias',
                    'Criar e gerenciar usuários',
                    'Ver todos os dados nacionais',
                    'Acessar todos os relatórios',
                    'Configurar sistema'
                ]
            ],
            'gestor' => [
                'description' => 'Gestão provincial/regional',
                'can_do' => [
                    'Visualizar dados da sua província',
                    'Cadastrar e editar hospitais da região',
                    'Gerenciar ambulâncias regionais',
                    'Ver casos da sua jurisdição',
                    'Gerar relatórios regionais'
                ]
            ],
            'medico' => [
                'description' => 'Acesso clínico completo',
                'can_do' => [
                    'Acessar fichas clínicas',
                    'Alterar status de tratamento',
                    'Emitir relatórios clínicos',
                    'Realizar triagem avançada',
                    'Gerenciar pontos de atendimento'
                ]
            ],
            'tecnico' => [
                'description' => 'Operações técnicas e triagem',
                'can_do' => [
                    'Realizar triagem de pacientes',
                    'Cadastrar novos casos',
                    'Acessar ficha clínica',
                    'Gerar QR Code',
                    'Gerenciar veículos/ambulâncias'
                ]
            ],
            'enfermeiro' => [
                'description' => 'Cuidados de enfermagem',
                'can_do' => [
                    'Visualizar pacientes',
                    'Cadastrar novos casos',
                    'Editar informações de pacientes',
                    'Acompanhar tratamentos'
                ]
            ],
            'condutor' => [
                'description' => 'Operação de ambulâncias',
                'can_do' => [
                    'Receber missões via API',
                    'Alterar status da ambulância',
                    'Ver rotas para hospitais',
                    'Atualizar localização'
                ]
            ],
            'visualizacao' => [
                'description' => 'Apenas leitura',
                'can_do' => [
                    'Visualizar dashboards',
                    'Acessar relatórios básicos',
                    'Ver estatísticas gerais'
                ]
            ]
        ];

        return $permissions[$papel] ?? ['description' => 'Papel não reconhecido', 'can_do' => []];
    }

    /**
     * Endpoints disponíveis por papel
     */
    private function getAvailableEndpoints(string $papel): array
    {
        $baseEndpoints = [
            'GET /api/v1/auth/me',
            'POST /api/v1/auth/logout',
            'POST /api/v1/auth/refresh'
        ];

        $endpointsByRole = [
            'administrador' => array_merge($baseEndpoints, [
                'GET|POST|PUT|DELETE /api/v1/hospitals/*',
                'GET|POST|PUT|DELETE /api/v1/ambulances/*',
                'GET|POST|PUT|DELETE /api/v1/patients/*',
                'POST /api/v1/auth/register',
                'GET /api/v1/reports/*',
                'PATCH /api/v1/ambulances/{id}/status',
                'POST /api/v1/ambulances/redirecionar'
            ]),
            'gestor' => array_merge($baseEndpoints, [
                'GET|POST|PUT /api/v1/hospitals/*',
                'GET /api/v1/patients/*',
                'GET /api/v1/reports/*'
            ]),
            'medico' => array_merge($baseEndpoints, [
                'GET|POST|PUT /api/v1/patients/*',
                'POST /api/v1/patients/triagem',
                'GET /api/v1/reports/*'
            ]),
            'tecnico' => array_merge($baseEndpoints, [
                'GET|POST|PUT /api/v1/patients/*',
                'POST /api/v1/patients/triagem',
                'GET|POST|PUT /api/v1/ambulances/*',
                'PATCH /api/v1/ambulances/{id}/status'
            ]),
            'enfermeiro' => array_merge($baseEndpoints, [
                'GET|POST|PUT /api/v1/patients/*'
            ]),
            'condutor' => array_merge($baseEndpoints, [
                'GET /api/v1/ambulances/*',
                'PATCH /api/v1/ambulances/{id}/status'
            ]),
            'visualizacao' => array_merge($baseEndpoints, [
                'GET /api/v1/patients/*',
                'GET /api/v1/reports/*'
            ])
        ];

        return $endpointsByRole[$papel] ?? $baseEndpoints;
    }
}