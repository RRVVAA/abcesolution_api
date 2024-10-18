<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComandaCliente extends Model
{
    use HasFactory;
    protected $fillable =[
        'empresa_id',
        'usuario_id',
        'nome',
        'logradouro',
        'numero',
        'bairro',
        'uf',
        'complemento',
        'telefone',
        'email',
        'cep',
        'ibge',
        'cidade',
    ];

    public function usuario()
    {
        return $this->belongsTo(ComandaUsuario::class);
    }

}

