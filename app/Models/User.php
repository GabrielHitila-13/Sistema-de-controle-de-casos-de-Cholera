<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'papel',
        'ativo',
        'ultimo_acesso',
        'estabelecimento_id',
        'veiculo_id',
        'permissoes_extras',
        'numero_licenca',
        'validade_licenca',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'ultimo_acesso' => 'datetime',
        'ativo' => 'boolean',
        'permissoes_extras' => 'array',
        'validade_licenca' => 'date',
    ];

    /**
     * Relacionamentos
     */
    public function estabelecimento()
    {
        return $this->belongsTo(Estabelecimento::class);
    }

    public function veiculo()
    {
        return $this->belongsTo(Veiculo::class);
    }

    /**
     * Scopes
     */
    public function scopePorPapel($query, $papel)
    {
        return $query->where('papel', $papel);
    }

    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    /**
     * Accessors
     */
    public function getPapelFormatadoAttribute()
    {
        return match($this->papel) {
            'administrador' => 'Administrador',
            'gestor' => 'Gestor Provincial',
            'medico' => 'Médico',
            'tecnico' => 'Técnico de Saúde',
            'enfermeiro' => 'Enfermeiro',
            'condutor' => 'Condutor de Ambulância',
            'visualizacao' => 'Usuário de Visualização',
            default => ucfirst($this->papel)
        };
    }

    /**
     * Verificar se o usuário tem um papel específico
     */
    public function temPapel($papel)
    {
        return $this->papel === $papel;
    }

    /**
     * Verificar se o usuário tem algum dos papéis especificados
     */
    public function temAlgumPapel(array $papeis)
    {
        return in_array($this->papel, $papeis);
    }

    /**
     * Verificar se pode gerenciar usuários
     */
    public function podeGerenciarUsuarios()
    {
        return $this->temAlgumPapel(['administrador', 'gestor']);
    }

    /**
     * Verificar se pode ver pacientes
     */
    public function podeVerPacientes()
    {
        return in_array($this->papel, ['administrador', 'gestor', 'medico', 'tecnico', 'enfermeiro']);
    }

    /**
     * Verificar se pode criar pacientes
     */
    public function podeCriarPacientes()
    {
        return $this->temAlgumPapel(['administrador', 'gestor', 'medico', 'tecnico', 'enfermeiro']);
    }

    /**
     * Verificar se pode editar pacientes
     */
    public function podeEditarPacientes()
    {
        return $this->temAlgumPapel(['administrador', 'gestor', 'medico', 'tecnico', 'enfermeiro']);
    }

    /**
     * Verificar se pode gerenciar estabelecimentos
     */
    public function podeGerenciarEstabelecimentos()
    {
        return in_array($this->papel, ['administrador', 'gestor']);
    }

    /**
     * Verificar se pode gerenciar veículos
     */
    public function podeGerenciarVeiculos()
    {
        return in_array($this->papel, ['administrador', 'gestor', 'tecnico']);
    }

    /**
     * Verificar se pode operar ambulância
     */
    public function podeOperarAmbulancia()
    {
        return $this->temPapel('condutor') && $this->veiculo_id;
    }

    /**
     * Verificar se pode ver relatórios
     */
    public function podeVerRelatorios()
    {
        return !$this->temPapel('condutor'); // Todos exceto condutor
    }

    /**
     * Verificar se pode fazer triagem
     */
    public function podeFazerTriagem()
    {
        return $this->temAlgumPapel(['medico', 'tecnico', 'enfermeiro']);
    }

    /**
     * Verificar se pode gerenciar ambulâncias
     */
    public function podeGerenciarAmbulancia()
    {
        return $this->temAlgumPapel(['administrador', 'gestor', 'tecnico', 'condutor']);
    }

    /**
     * Verificar se pode ver dados sensíveis
     */
    public function podeVerDadosSensiveis()
    {
        return $this->temAlgumPapel(['administrador', 'gestor', 'medico']);
    }

    /**
     * Verificar se a licença está válida
     */
    public function licencaValida()
    {
        return $this->validade_licenca && $this->validade_licenca->isFuture();
    }

    /**
     * Obter permissões do usuário
     */
    public function getPermissoesAttribute()
    {
        $permissoes = [];

        switch ($this->papel) {
            case 'administrador':
                $permissoes = [
                    'view-dashboard', 'create-pacientes', 'edit-pacientes', 'delete-pacientes',
                    'view-pacientes', 'fazer-triagem', 'view-estabelecimentos', 
                    'create-estabelecimentos', 'edit-estabelecimentos', 'delete-estabelecimentos',
                    'view-usuarios', 'create-usuarios', 'edit-usuarios', 'delete-usuarios',
                    'view-veiculos', 'create-veiculos', 'edit-veiculos', 'delete-veiculos',
                    'view-relatorios', 'export-relatorios', 'manage-system'
                ];
                break;

            case 'gestor':
                $permissoes = [
                    'view-dashboard', 'create-pacientes', 'edit-pacientes', 'view-pacientes',
                    'fazer-triagem', 'view-estabelecimentos', 'edit-estabelecimentos',
                    'view-usuarios', 'create-usuarios', 'edit-usuarios',
                    'view-veiculos', 'edit-veiculos', 'view-relatorios', 'export-relatorios'
                ];
                break;

            case 'medico':
                $permissoes = [
                    'view-dashboard', 'create-pacientes', 'edit-pacientes', 'view-pacientes',
                    'fazer-triagem', 'view-estabelecimentos', 'view-relatorios'
                ];
                break;

            case 'tecnico':
                $permissoes = [
                    'view-dashboard', 'create-pacientes', 'edit-pacientes', 'view-pacientes',
                    'fazer-triagem', 'view-estabelecimentos', 'view-veiculos', 'edit-veiculos'
                ];
                break;

            case 'enfermeiro':
                $permissoes = [
                    'view-dashboard', 'view-pacientes', 'edit-pacientes', 'fazer-triagem',
                    'view-estabelecimentos'
                ];
                break;

            case 'condutor':
                $permissoes = [
                    'view-dashboard', 'view-veiculos', 'edit-veiculo-proprio', 'view-missoes'
                ];
                break;

            case 'visualizacao':
                $permissoes = [
                    'view-dashboard', 'view-relatorios'
                ];
                break;
        }

        // Adicionar permissões extras se existirem
        if ($this->permissoes_extras) {
            $permissoes = array_merge($permissoes, $this->permissoes_extras);
        }

        return array_unique($permissoes);
    }
}
