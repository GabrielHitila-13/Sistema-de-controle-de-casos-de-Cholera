<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Veiculo extends Model
{
    use HasFactory;

    protected $fillable = [
        'placa',
        'tipo',
        'status',
        'descricao'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Scopes para filtrar por status
    public function scopeDisponivel($query)
    {
        return $query->where('status', 'disponivel');
    }

    public function scopeEmAtendimento($query)
    {
        return $query->where('status', 'em_atendimento');
    }

    public function scopeManutencao($query)
    {
        return $query->where('status', 'manutencao');
    }
}
