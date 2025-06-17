<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PontoAtendimento extends Model
{
    use HasFactory;

    protected $table = 'pontos_atendimento';

    protected $fillable = [
        'nome',
        'latitude',
        'longitude',
        'descricao',
        'ativo'
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'ativo' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Scope para pontos ativos
    public function scopeAtivo($query)
    {
        return $query->where('ativo', true);
    }

    // Accessor para URL do Google Maps
    public function getGoogleMapsUrlAttribute()
    {
        return "https://maps.google.com/?q={$this->latitude},{$this->longitude}";
    }
}
