<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Paciente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'bi',
        'telefone',
        'data_nascimento',
        'sexo',
        'estabelecimento_id',
        'sintomas' => 'array',
        'risco',
        'data_triagem',
        'qr_code',
    ];

    protected $casts = [
        'data_nascimento' => 'date',
        'data_triagem' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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
    public function scopeAltoRisco($query)
    {
        return $query->where('risco', 'alto');
    }

    public function scopeMedioRisco($query)
    {
        return $query->where('risco', 'medio');
    }

    public function scopeBaixoRisco($query)
    {
        return $query->where('risco', 'baixo');
    }

    public function scopePorEstabelecimento($query, $estabelecimentoId)
    {
        return $query->where('estabelecimento_id', $estabelecimentoId);
    }

    /**
     * Accessors
     */
    public function getIdadeAttribute()
    {
        return $this->data_nascimento->age;
    }

    public function getRiscoFormatadoAttribute()
    {
        return ucfirst($this->risco);
    }

    public function getSexoFormatadoAttribute()
    {
        return ucfirst($this->sexo);
    }

    /**
     * Verificar se é caso urgente
     */
    public function isUrgente()
    {
        return $this->risco === 'alto';
    }

    /**
     * Verificar se precisa de atenção
     */
    public function precisaAtencao()
    {
        return in_array($this->risco, ['alto', 'medio']);
    }

    /**
     * Obter cor do badge baseado no risco
     */
    public function getCorRiscoAttribute()
    {
        return match($this->risco) {
            'alto' => 'red',
            'medio' => 'yellow',
            'baixo' => 'green',
            default => 'gray'
        };
    }

    /**
     * Obter classe CSS do badge baseado no risco
     */
    public function getClasseRiscoAttribute()
    {
        return match($this->risco) {
            'alto' => 'badge-alto',
            'medio' => 'badge-medio',
            'baixo' => 'badge-baixo',
            default => 'badge-baixo'
        };
    }
}
