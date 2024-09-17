<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Movimento extends Model
{
    use HasFactory;
    protected $fillable = [
        "id",
        "empresa_id",
        "tipo_movimento_id",
        "produto_id",
        "compra_id",
        "loja_pedido_id",
        "entrada_avulsa_id",
        "saida_avulsa_id",
        "venda_id",
        "pdvvenda_id",
        "nfe_id",    
        "ordem_producao_id",
        "ent_sai",
        "estorno",
        "data_movimento",
        "qtde_movimento",
        "valor_movimento",
        "subtotal_movimento",
        "descricao",
        "saldoestoque"
    ];
    
      
    public function tipoMovimento(){
        return $this->belongsTo(TipoMovimento::class,"tipo_movimento_id","id");
    }
    
    public function produto(){
        return $this->belongsTo(Produto::class,"produto_id","id");
    }
    public function empresa(){
        return $this->belongsTo(Empresa::class,"empresa_id","id");
    }
         
    
    public static function saldoEstoque($produto_id){
        $resultado = Movimento::where("produto_id", $produto_id)
        ->select("saldoestoque as saldo")
        ->orderBy("id","Desc")
        ->limit(1)
        ->first() ;
        
        return ($resultado) ? $resultado->saldo : 0;
    }
}
