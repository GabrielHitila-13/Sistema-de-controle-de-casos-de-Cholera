<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gabinete extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'tipo',
        'latitude',
        'longitude',
        'endereco',
        'telefone'
    ];

    public function estabelecimentos()
    {
        return $this->hasMany(Estabelecimento::class);
    }
}