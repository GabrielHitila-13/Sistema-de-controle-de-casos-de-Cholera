<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

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
        'ultimo_acesso',
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
    ];

    /**
     * Relacionamentos
     */
    public function estabelecimento()
    {
        return $this->belongsTo(Estabelecimento::class);
    }

    /**
     * Scopes
     */
    public function scopePorPapel($query, $papel)
    {
        return $query->where('papel', $papel);
    }

    /**
     * Accessors
     */
    public function getPapelFormatadoAttribute()
    {
        return ucfirst($this->papel);
    }

    /**
     * Verificar se o usuário tem um papel específico
     */
    public function temPapel($papel)
    {
        return $this->papel === $papel;
    }

    /**
     * Verificar se o usuário tem um dos papéis especificados
     */
    public function temAlgumPapel(array $papeis)
    {
        return in_array($this->papel, $papeis);
    }

    /**
     * Verificar se é administrador
     */
    public function isAdmin()
    {
        return $this->papel === 'administrador';
    }

    /**
     * Verificar se pode gerenciar usuários
     */
    public function podeGerenciarUsuarios()
    {
        return $this->papel === 'administrador';
    }

    /**
     * Verificar se pode ver pacientes
     */
    public function podeVerPacientes()
    {
        return in_array($this->papel, ['administrador', 'gestor', 'medico', 'tecnico', 'enfermeiro']);
    }

    /**
     * Verificar se pode gerenciar estabelecimentos
     */
    public function podeGerenciarEstabelecimentos()
    {
        return in_array($this->papel, ['administrador', 'gestor']);
    }
}
