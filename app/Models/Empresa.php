<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Empresa extends Model
{
    use HasFactory;
    public $fillable = ['razao_social', 'subdominio', 'pasta', 'cpf_cnpj','cep',
        'logradouro','numero','bairro','complemento','uf',
        'cidade','fone','email',
        'celular',
        'status_id',
        'status_plano_id',
        'plano_preco_id',
        "forma_pagto_id",
        'data_aquisicao',
        "valor_contrato",
        "data_vencimento",
        "tipo_recorrencia",
        "data_inicial_vencimento",
        "valor_recorrente",
        "dias_bloqueia",
        "logo"

    ];


    public function planopreco(){
        return $this->belongsTo(PlanoPreco::class,"plano_preco_id");
    }

    public function parametro(){
        return $this->hasOne(Parametro::class, 'empresa_id', 'id');
    }

    public function lojaConfiguracao(){
        return $this->hasOne(LojaConfiguracao::class, 'empresa_id', 'id');
    }

}
