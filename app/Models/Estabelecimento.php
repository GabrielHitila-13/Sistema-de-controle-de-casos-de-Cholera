<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estabelecimento extends Model
{
    use HasFactory;

    protected $fillable = [
        'gabinete_id',
        'nome',
        'categoria',
        'endereco',
        'telefone',
        'capacidade'
    ];

    public function gabinete()
    {
        return $this->belongsTo(Gabinete::class);
    }

    public function pacientes()
    {
        return $this->hasMany(Paciente::class);
    }

    public function usuarios()
    {
        return $this->hasMany(User::class);
    }
}