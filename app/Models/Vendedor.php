<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Tenant\Traits\EmpresaTrait;

class Vendedor extends Model
{
    use HasFactory;
    protected $fillable =[
        'empresa_id',
        'uuid',
        'nome',
        'cpf',
        'rg',
        'nascimento',
        'logradouro',
        'complemento',
        'numero',
        'bairro',
        'telefone',
        'celular',
        'email',
        'uf',
        'cep',
        'ibge',
        'cidade',
        'senha',
        'nascimento',
        'status_id',
        "password"
    ];
    
    public function empresa(){
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }
    public function status(){
        return $this->belongsTo(Status::class, 'status_id');
    }
}
