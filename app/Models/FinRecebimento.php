<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FinRecebimento extends Model
{
    use HasFactory;
    protected $fillable =[
        'id',
        "empresa_id",
        "usuario_id",
        "descricao_recebimento",
        "tipo_documento",
        "documento_id",
        'conta_receber_id',
        'conta_corrente_id',
        'classificacao_financeira_id',
        "forma_pagto_id",
        "data_recebimento",
        "numero_documento",
        "valor_original",
        "valor_a_receber",
        "valor_recebido",
        "juros",
        "desconto",
        "multa",
        "status_id",
        "desconto",
    ];
    
    public function usuario(){
        return $this->belongsTo(User::class, 'usuario_id');
    }
    
    public function venda(){
        return $this->belongsTo(Venda::class, 'venda_id');
    }
    
    public function forma_pagamento(){
        return $this->belongsTo(FormaPagto::class, 'forma_pagto_id');
    }
    
    public function conta_receber(){
        return $this->belongsTo(FinContaReceber::class, 'conta_receber_id');
    }
    
    public function cliente(){
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }
    
   
}
