<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MercadoPagoTransacao extends Model
{
    use HasFactory;
    protected $fillable = [
        'id', 'empresa_id', 'cobranca_id', 'fatura_id', 'loja_pedido_id', 'pdv_venda_id',
        'transacao_id', 'status','descricao','data_criacao','data_ultima_modificacao','data_expiracao',
        'data_aprovacao', 'valor', 'metodo_pagamento','referencia_externa', 'planopreco_id','assinatura_id','delivery_id'
    ];
}
