<?php
namespace App\Services;


use App\Models\CupomDesconto;
use App\Models\Produto;
use App\Models\LojaPedido;
use App\Models\LojaItemPedido;

class CupomDescontoService
{       
    
    public static function validarCupom($cupom, $produto){   
            $aplicar = true;
            // verifica a validade do cupom
            if($cupom->data_validade){
                if($cupom->data_validade < hoje()){
                    $aplicar = false;
                }
            }
            
            // Validar pela quantidade
            if($cupom->ativo != "S" ){
                $aplicar = false;
            }
            
            if($cupom->produto_id ){
                if($cupom->produto->id != $produto->id){
                    $aplicar = false;
                }
            }
            
            if($cupom->categoria_id ){
                if($cupom->categoria_id != $produto->categoria_id){
                    $aplicar = false;
                }else{
                    $aplicar = true;
                }
            }
        
        
        return $aplicar;
    }
    public static function aplicarCupom($dados){
        $retorno = new \stdClass();
        $retorno->tem_erro = true;
        $retorno->cupom_desconto_id = null;
        $cupom = CupomDesconto::where("codigo", $dados->codigo_cupom)->first();
        $pedido = LojaPedido::find($dados->pedido_id);
        
        if(!$pedido){
            $retorno->erro = "Este Pedido não é válido ou não foi encontrado!";
            return $retorno;
        }        
        
        if(!$cupom){
            $retorno->erro = "Este Cupom não é válido ou não foi encontrado!";
            return $retorno;            
        }        
        
        if($pedido->cupom_desconto_id){
            $retorno->erro = "Não é mais possível usar um cupom para esta venda!";
            return $retorno;
        }
        
        foreach ($pedido->itens as $item){
            $aplicar = true;
            $produto = $item->produto;           
            $item->desconto_por_unidade  = 0;
            $retorno->desconto_por_valor = $cupom->desconto_por_valor;
            $item->desconto_percentual   = $cupom->desconto_percentual;
            $item->cupom_desconto_id     = $cupom->id;
            
            // verifica a validade do cupom
            if($cupom->data_validade){
                if($cupom->data_validade < hoje()){
                    $aplicar = false;
                }
            }
            
            // Validar pela quantidade
            if($cupom->ativo != "S" ){
                $aplicar = false;
            }
            
            if($cupom->produto_id ){
                if($cupom->produto->id != $produto->id){
                    $aplicar = false;
                }
            }
            
            if($cupom->categoria_id ){
                if($cupom->categoria_id != $produto->categoria_id){
                    $aplicar = false;
                }
            }
            
            if($aplicar==true){
                if($cupom->desconto_percentual > 0){
                    $item->desconto_por_unidade  = $cupom->desconto_percentual * $produto->valor_venda * 0.01;
                    
                }
                
                if($cupom->desconto_por_valor > 0){
                    $item->desconto_por_unidade = $cupom->desconto_por_valor;                    
                }
                
                $item->total_desconto_item      =  $item->desconto_por_unidade * $item->quantidade;
                $item->subtotal_liquido         =  ($item->valor - $item->desconto_por_unidade )  * $item->quantidade;
                $salvar = $item->save();
                if($salvar){
                    $pedido->cupom_desconto_id = $cupom->id;
                    $pedido->save();
                }
            }
            
        }

        $total_desconto = LojaItemPedido::where("pedido_id", $pedido->id )->sum("total_desconto_item");
        $pedido->valor_desconto_cupom   = $total_desconto;
        $pedido->valor_liquido          = $pedido->valor_venda + $pedido->valor_frete - $total_desconto;
        $pedido->save();
        return $pedido->id;
    }
    
    
    public static function excluirCupom($pedido_id){
        $retorno = new \stdClass();
        $retorno->tem_erro = true;
        $pedido = LojaPedido::find($pedido_id);      
        
        
        foreach ($pedido->itens as $item){      
           
            $item->desconto_por_unidade  = 0;
            $item->desconto_por_valor    = 0;
            $item->desconto_percentual   = 0;
            $item->cupom_desconto_id     = null;  
            $item->total_desconto_item   =  0;
            $item->subtotal_liquido      =  $item->valor * $item->quantidade;            
            $item->save();
        }
        
        $pedido->cupom_desconto_id = null;
        $pedido->save();
        LojaPedido::somarTotal($pedido->id);        
        return $pedido->id;
    }
    
    
    public static function aplicarCupomNoProduto($dados){
        $retorno = new \stdClass();
        $retorno->tem_erro = true;
        $retorno->cupom_desconto_id = null;
        $cupom = CupomDesconto::where("codigo", $dados->codigo)->first();
        
        if(!$cupom){
            $retorno->erro = "Este Cupom não é válido ou não foi encontrado!";
            return $retorno;
        }
        
        $produto = Produto::find($dados->produto_id);
        $retorno->desconto_por_unidade  = 0;
        $retorno->desconto_por_valor    = $cupom->desconto_por_valor;
        $retorno->desconto_percentual   = $cupom->desconto_percentual;
        $retorno->cupom_desconto_id     = $cupom->id;
        // verifica a validade do cupom
        if($cupom->data_validade){
            if($cupom->data_validade < hoje()){
                $retorno->erro = "Este Cupom está vencido";
                return $retorno;
            }
        }
        
        // Validar pela quantidade
        
        if($cupom->ativo != "S" ){
            $retorno->erro = "Este Cupom Não está mais ativo";
            return $retorno;
        }
        
        if($cupom->produto_id ){
            if($cupom->produto->id != $produto->id){
                $retorno->erro = "Este Cupom não serve para este produto";
                return $retorno;
            }
        }
        
        if($cupom->categoria_id ){
            if($cupom->categoria_id != $dados->produto_id){
                $retorno->erro = "Este Cupom não serve para este produto";
                return $retorno;
            }
        }
        
        if($cupom->desconto_percentual > 0){
            $retorno->desconto_por_unidade  = $cupom->desconto_percentual * $produto->valor_venda * 0.01;
            
        }
        
        if($cupom->desconto_por_valor > 0){
            $retorno->desconto_por_unidade = $cupom->desconto_por_valor;
            
        }
        
        $retorno->total_desconto_item  =  $retorno->desconto_por_unidade * $dados->qtde;
        $retorno->subtotal_liquido     =  ($produto->valor_venda - $retorno->desconto_por_unidade )  * $dados->qtde;
        $retorno->item_com_desconto    =  formataNumeroBr($produto->valor_venda - $retorno->desconto_por_unidade);
        $retorno->tem_erro             = false;
        return $retorno;
    }
}

