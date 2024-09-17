<?php
namespace App\Services;

use App\Models\GradeProduto;

class GradeService{
    
    public static function montar($produto_id){
        $retorno            = new \stdClass();
        $linhas             = GradeProduto::where("produto_id", $produto_id)->distinct()->with('linha')->get(["linha_id"]);
        $grade              = GradeProduto::where(["produto_id" =>$produto_id])->first();
        if($grade){
            $retorno->variacao_linha  = $grade->variacao_linha->variacao;
            $retorno->variacao_coluna = $grade->variacao_coluna->variacao;
            foreach($linhas as $l){
                $retorno->linhas[] = (object) array(
                    "id"    => $l->linha->id,
                    "valor" => $l->linha->valor,
                    
                );
            }
            
            $colunas    = GradeProduto::where("produto_id", $produto_id)->distinct()->with('coluna')->get(["coluna_id"]);
            foreach($colunas as $c){
                $retorno->colunas[] = (object) array(
                    "id"    => $c->coluna->id,
                    "valor" => $c->coluna->valor,
                    
                );
            }
            
            foreach($retorno->linhas as $l){
                foreach($retorno->colunas as $c){
                    $grade = GradeProduto::where(["linha_id"=>$l->id, "coluna_id"=>$c->id,"produto_id"=>$produto_id])->first();
                    if($grade){
                        $retorno->grade[$l->id][$c->id] = (object) array(
                            "id"           => $grade->id,
                            "codigo_barra" => $grade->codigo_barra,
                            "nome_produto" => $grade->produto->nome . " " . $grade->linha->valor ." / " . $grade->coluna->valor,
                            "sku"          => $grade->sku,
                            "estoque"      => $grade->estoque
                        );
                    }else{
                        $retorno->grade[$l->id][$c->id] = (object) array(
                            "id"           => -1,
                            "codigo_barra" => null,
                            "nome_produto" => "Sen produto",
                            "sku"          => null,
                            "estoque"      => 0
                        );
                    }
                    
                }
            }
        }
        
       
        
        return $retorno;
        
    }
    
    
}

