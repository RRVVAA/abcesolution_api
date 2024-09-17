<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdutoSemelhante extends Model
{
    use HasFactory;
    protected $fillable = [
        'produto_principal_id',
        'produto_semelhante_id',
    ];
    
    public function produtoPrincipal(){
        return $this->belongsTo(Produto::class,"produto_principal_id");
    }
    
    public function produtoSemelhante(){
        return $this->belongsTo(Produto::class,"produto_semelhante_id");
    }
    
    public static function lista($id){
        return ProdutoSemelhante::where("produto_principal_id", $id)
        ->join("produtos", "produtos.id","=", "produto_semelhantes.produto_semelhante_id")
        ->select("produto_semelhantes.*",
            "produtos.id as semelhante_id",
            "produtos.nome",
            "produtos.uuid",
            "produtos.valor_venda",
            "produtos.imagem"
            )
        ->get() ;
    }
}
