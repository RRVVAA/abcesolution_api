<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GradeMovimento extends Model
{
    use HasFactory;
    protected $fillable = [
        "id",
        "empresa_id",
        "grade_id",
        "status_id",
        "estorno",
        "tipo_movimento_id",
        "produto_id",
        "item_compra_id",
        "item_venda_id",
        "item_pdvvenda_id",
        "item_loja_pedido_id",
        "entrada_avulsa_id",
        "saida_avulsa_id",
        "loja_pedido_id",
        'pdvvenda_id',
        "ent_sai",
        "data_movimento",
        "qtde_movimento",
        "valor_movimento",
        "subtotal_movimento",
        "descricao",
        "saldoestoque",
        "item_nfe_id"
    ];
    
    
    
    public function grade(){
        return $this->belongsTo(GradeProduto::class,"grade_id","id");
    }
    
  
    
   
}
