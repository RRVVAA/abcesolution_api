<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TributacaoProduto extends Model
{
    use HasFactory;
    protected $fillable = [
        'natureza_operacao_id',
        'produto_id',
        'tributacao_id',
    ];
    
    public function produto(){
        return $this->belongsTo(Produto::class,"produto_id");
    }
    public function tributacao(){
        return $this->belongsTo(Tributacao::class,"tributacao_id");
    }
    
    public static function lista($produto_id){
        $lista = TributacaoProduto::where("produto_id", $produto_id)->get();
        $resultado = array();
        if($lista){
            foreach ($lista as $l){
                $t                  = new \stdClass();
                $t->id              = $l->id;
                $t->produto_id      = $l->produto_id;
                $t->tributacao_id   = $l->tributacao_id;
                $t->natureza_operacao_id = $l->natureza_operacao_id;
                $t->nome            = $l->produto->nome;
                $t->descricao       = $l->tributacao->descricao;
                $t->cfop            = $l->tributacao->cfop;
                $t->cstIPI          = $l->tributacao->cstIPI;
                $t->cstPIS          = $l->tributacao->cstPIS;
                $t->cstCOFINS       = $l->tributacao->cstCOFINS;
                $t->cstICMS         = $l->tributacao->cstICMS;
                $resultado[]        = $t;            
            }
        }
        
        return (object) $resultado;
    }
    
    public static function listaPorTributacao($tributacao_id){
        $lista = TributacaoProduto::where("tributacao_id", $tributacao_id)->get();
        $resultado = array();
        if($lista){
            foreach ($lista as $l){
                $t                  = new \stdClass();
                $t->id              = $l->id;
                $t->produto_id      = $l->produto_id;
                $t->tributacao_id   = $l->tributacao_id;
                $t->natureza_operacao_id = $l->natureza_operacao_id;
                $t->descricao       = $l->tributacao->descricao;
                $t->nome            = $l->produto->nome;
                $t->cfop            = $l->tributacao->cfop;
                $t->cstIPI          = $l->tributacao->cstIPI;
                $t->cstPIS          = $l->tributacao->cstPIS;
                $t->cstCOFINS       = $l->tributacao->cstCOFINS;
                $t->cstICMS         = $l->tributacao->cstICMS;
                $resultado[]        = $t;
            }
        }
        
        return (object) $resultado;
    }
}
