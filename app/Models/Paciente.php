<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Paciente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'bi',
        'telefone',
        'data_nascimento',
        'idade',
        'sexo',
        'endereco',
        'estabelecimento_id',
        'sintomas',
        'observacoes',
        'risco',
        'status',
        'prioridade',
        'data_triagem',
        'qr_code',
        
        // Cholera-specific fields
        'diagnostico_colera',
        'probabilidade_colera',
        'data_diagnostico',
        'sintomas_colera',
        'fatores_risco',
        'recomendacoes',
        'numero_caso',
        'contato_caso_confirmado',
        'area_surto',
        'agua_contaminada',
        
        // Vehicle and hospital assignment
        'veiculo_id',
        'hospital_destino_id',
        'ponto_atendimento_id',
    ];

    protected $casts = [
        'data_nascimento' => 'date',
        'data_triagem' => 'datetime',
        'data_diagnostico' => 'datetime',
        'contato_caso_confirmado' => 'boolean',
        'area_surto' => 'boolean',
        'agua_contaminada' => 'boolean',
        'probabilidade_colera' => 'decimal:2',
        'sintomas_colera' => 'array',
        'fatores_risco' => 'array',
    ];

    protected $attributes = [
        'risco' => 'baixo',
        'status' => 'aguardando',
        'prioridade' => 'media',
        'diagnostico_colera' => 'pendente',
        'contato_caso_confirmado' => false,
        'area_surto' => false,
        'agua_contaminada' => false,
    ];

    /**
     * Boot method to handle model events
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($paciente) {
            // Calculate age from birth date if not provided
            if (!$paciente->idade && $paciente->data_nascimento) {
                $paciente->idade = Carbon::parse($paciente->data_nascimento)->age;
            }
            
            // Set default values for required fields if not provided
            $paciente->endereco = $paciente->endereco ?? '';
            $paciente->sintomas = $paciente->sintomas ?? '';
            $paciente->observacoes = $paciente->observacoes ?? '';
            
            // Generate case number if not provided
            if (!$paciente->numero_caso) {
                $paciente->numero_caso = 'CASO-' . date('Y') . '-' . str_pad(
                    (static::whereYear('created_at', date('Y'))->count() + 1), 
                    6, '0', STR_PAD_LEFT
                );
            }
        });
        
        static::updating(function ($paciente) {
            // Recalculate age if birth date changed
            if ($paciente->isDirty('data_nascimento') && $paciente->data_nascimento) {
                $paciente->idade = Carbon::parse($paciente->data_nascimento)->age;
            }
        });
    }

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

    public function hospitalDestino()
    {
        return $this->belongsTo(Estabelecimento::class, 'hospital_destino_id');
    }

    public function pontoAtendimento()
    {
        return $this->belongsTo(PontoAtendimento::class);
    }

    /**
     * Scopes
     */
    public function scopePorRisco($query, $risco)
    {
        return $query->where('risco', $risco);
    }

    public function scopePorDiagnostico($query, $diagnostico)
    {
        return $query->where('diagnostico_colera', $diagnostico);
    }

    public function scopeRecentes($query, $dias = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($dias));
    }

    public function scopeComColera($query)
    {
        return $query->whereIn('diagnostico_colera', ['confirmado', 'provavel']);
    }

    public function scopePorStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePorPrioridade($query, $prioridade)
    {
        return $query->where('prioridade', $prioridade);
    }

    /**
     * Accessors
     */
    public function getRiscoFormatadoAttribute()
    {
        return match($this->risco) {
            'alto' => 'Alto Risco',
            'medio' => 'Médio Risco',
            'baixo' => 'Baixo Risco',
            default => ucfirst($this->risco)
        };
    }

    public function getDiagnosticoColeraFormatadoAttribute()
    {
        return match($this->diagnostico_colera) {
            'confirmado' => 'Confirmado',
            'provavel' => 'Provável',
            'suspeito' => 'Suspeito',
            'descartado' => 'Descartado',
            'pendente' => 'Pendente',
            default => ucfirst($this->diagnostico_colera)
        };
    }

    public function getStatusFormatadoAttribute()
    {
        return match($this->status) {
            'aguardando' => 'Aguardando',
            'em_atendimento' => 'Em Atendimento',
            'finalizado' => 'Finalizado',
            'transferido' => 'Transferido',
            default => ucfirst($this->status)
        };
    }

    public function getPrioridadeFormatadaAttribute()
    {
        return match($this->prioridade) {
            'critica' => 'Crítica',
            'alta' => 'Alta',
            'media' => 'Média',
            'baixa' => 'Baixa',
            default => ucfirst($this->prioridade)
        };
    }

    public function getSexoFormatadoAttribute()
    {
        return $this->sexo === 'masculino' ? 'Masculino' : 'Feminino';
    }

    public function getIdadeFormatadaAttribute()
    {
        return $this->idade ? $this->idade . ' anos' : 'N/A';
    }

    public function getDataTriagemFormatadaAttribute()
    {
        return $this->data_triagem ? $this->data_triagem->format('d/m/Y H:i') : 'N/A';
    }

    public function getDataDiagnosticoFormatadaAttribute()
    {
        return $this->data_diagnostico ? $this->data_diagnostico->format('d/m/Y H:i') : 'N/A';
    }

    public function getEnderecoFormatadoAttribute()
    {
        return $this->endereco ?: 'Não informado';
    }

    /**
     * Métodos auxiliares
     */
    public function isAltoRisco()
    {
        return $this->risco === 'alto';
    }

    public function isColeraConfirmada()
    {
        return $this->diagnostico_colera === 'confirmado';
    }

    public function isColeraProvavel()
    {
        return $this->diagnostico_colera === 'provavel';
    }

    public function isColeraSuspeita()
    {
        return $this->diagnostico_colera === 'suspeito';
    }

    public function temColera()
    {
        return in_array($this->diagnostico_colera, ['confirmado', 'provavel']);
    }

    public function getStatusUrgencia()
    {
        if ($this->isColeraConfirmada() && $this->isAltoRisco()) {
            return 'critico';
        } elseif ($this->isColeraConfirmada() || $this->isAltoRisco()) {
            return 'urgente';
        } elseif ($this->isColeraProvavel()) {
            return 'atencao';
        } else {
            return 'normal';
        }
    }

    public function getCorStatus()
    {
        return match($this->getStatusUrgencia()) {
            'critico' => 'danger',
            'urgente' => 'warning',
            'atencao' => 'info',
            default => 'success'
        };
    }

    public function getCorRisco()
    {
        return match($this->risco) {
            'alto' => 'danger',
            'medio' => 'warning',
            'baixo' => 'success',
            default => 'secondary'
        };
    }

    public function getCorPrioridade()
    {
        return match($this->prioridade) {
            'critica' => 'danger',
            'alta' => 'warning',
            'media' => 'info',
            'baixa' => 'success',
            default => 'secondary'
        };
    }
}
