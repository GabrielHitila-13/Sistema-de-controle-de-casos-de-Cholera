<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Paciente extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'bi',
        'telefone_encrypted',
        'data_nascimento',
        'sexo',
        'sintomas',
        'risco',
        'qr_code',
        'estabelecimento_id',
        'data_triagem'
    ];

    protected $casts = [
        'data_nascimento' => 'date',
        'data_triagem' => 'datetime'
    ];

    public function estabelecimento()
    {
        return $this->belongsTo(Estabelecimento::class);
    }

    // Accessor para descriptografar telefone
    public function getTelefoneAttribute()
    {
        return $this->telefone_encrypted ? Crypt::decryptString($this->telefone_encrypted) : null;
    }

    // Mutator para criptografar telefone
    public function setTelefoneAttribute($value)
    {
        $this->attributes['telefone_encrypted'] = $value ? Crypt::encryptString($value) : null;
    }

    // Método para calcular risco baseado em sintomas
    public function calcularRisco()
    {
        $sintomas = strtolower($this->sintomas ?? '');
        $pontuacao = 0;

        // Sintomas de alto risco
        if (str_contains($sintomas, 'diarreia aquosa')) $pontuacao += 3;
        if (str_contains($sintomas, 'vomito')) $pontuacao += 2;
        if (str_contains($sintomas, 'desidratacao')) $pontuacao += 3;
        if (str_contains($sintomas, 'febre')) $pontuacao += 1;
        if (str_contains($sintomas, 'dor abdominal')) $pontuacao += 1;

        if ($pontuacao >= 5) return 'alto';
        if ($pontuacao >= 3) return 'medio';
        return 'baixo';
    }
}