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
        'estabelecimento_id',
        'modelo',
        'ano',
        'equipamentos',
        'latitude',
        'longitude',
        'ultima_manutencao',
        'quilometragem',
    ];

    protected $casts = [
        'equipamentos' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'ultima_manutencao' => 'datetime',
        'quilometragem' => 'integer',
        'ano' => 'integer',
    ];

    /**
     * Relacionamentos
     */
    public function estabelecimento()
    {
        return $this->belongsTo(Estabelecimento::class);
    }

    public function condutor()
    {
        return $this->hasOne(User::class, 'veiculo_id');
    }

    /**
     * Scopes
     */
    public function scopeDisponiveis($query)
    {
        return $query->where('status', 'disponivel');
    }

    public function scopeAmbulancias($query)
    {
        return $query->where('tipo', 'ambulancia');
    }

    public function scopePorEstabelecimento($query, $estabelecimentoId)
    {
        return $query->where('estabelecimento_id', $estabelecimentoId);
    }

    /**
     * Accessors
     */
    public function getStatusFormatadoAttribute()
    {
        return match($this->status) {
            'disponivel' => 'Disponível',
            'em_atendimento' => 'Em Atendimento',
            'manutencao' => 'Manutenção',
            'indisponivel' => 'Indisponível',
            default => ucfirst($this->status)
        };
    }

    public function getTipoFormatadoAttribute()
    {
        return match($this->tipo) {
            'ambulancia' => 'Ambulância',
            'apoio' => 'Veículo de Apoio',
            default => ucfirst($this->tipo)
        };
    }

    public function getEquipamentosFormatadosAttribute()
    {
        if (!$this->equipamentos || !is_array($this->equipamentos)) {
            return 'Nenhum equipamento cadastrado';
        }

        return implode(', ', $this->equipamentos);
    }

    public function getLocalizacaoAttribute()
    {
        if ($this->latitude && $this->longitude) {
            return "Lat: {$this->latitude}, Lng: {$this->longitude}";
        }
        return 'Localização não disponível';
    }

    public function getUltimaManutencaoFormatadaAttribute()
    {
        return $this->ultima_manutencao ? $this->ultima_manutencao->format('d/m/Y') : 'Nunca';
    }

    public function getQuilometragemFormatadaAttribute()
    {
        return number_format($this->quilometragem, 0, ',', '.') . ' km';
    }

    /**
     * Métodos auxiliares
     */
    public function isDisponivel()
    {
        return $this->status === 'disponivel';
    }

    public function isEmAtendimento()
    {
        return $this->status === 'em_atendimento';
    }

    public function isEmManutencao()
    {
        return $this->status === 'manutencao';
    }

    public function isIndisponivel()
    {
        return $this->status === 'indisponivel';
    }

    public function isAmbulancia()
    {
        return $this->tipo === 'ambulancia';
    }

    public function temCondutor()
    {
        return $this->condutor()->exists();
    }

    public function precisaManutencao()
    {
        if (!$this->ultima_manutencao) {
            return true;
        }

        // Considera que precisa manutenção se passou mais de 90 dias
        return $this->ultima_manutencao->diffInDays(now()) > 90;
    }

    public function getCorStatus()
    {
        return match($this->status) {
            'disponivel' => 'success',
            'em_atendimento' => 'primary',
            'manutencao' => 'warning',
            'indisponivel' => 'danger',
            default => 'secondary'
        };
    }

    public function getDistanciaAte($latitude, $longitude)
    {
        if (!$this->latitude || !$this->longitude) {
            return null;
        }

        // Fórmula de Haversine para calcular distância entre dois pontos
        $earthRadius = 6371; // Raio da Terra em km

        $latFrom = deg2rad($this->latitude);
        $lonFrom = deg2rad($this->longitude);
        $latTo = deg2rad($latitude);
        $lonTo = deg2rad($longitude);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos($latFrom) * cos($latTo) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c; // Distância em km
    }
}
