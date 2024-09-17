<?php
namespace App\Services;

use App\Models\Movimento;
use App\Models\PdvItemVenda;
use App\Models\PdvVenda;
use App\Models\LojaItemPedido;
use App\Models\LojaPedido;
use App\Models\GradeMovimento;

class MovimentoService{
    public static function inserir($mov ){
        $saldo_anterior = Movimento::saldoEstoque($mov->produto_id);
        $qtde           = ($mov->ent_sai=="E") ? $mov->qtde_movimento : - $mov->qtde_movimento;
        $saldo          =  $saldo_anterior + ($qtde) ;
        
        //Se for transferência
        $saldo_atual = ($mov->tipo_movimento_id==config('constantes.tipo_movimento.ENTRADA_TRANSFERENCIA_ESTOQUE') || $mov->tipo_movimento_id==config('constantes.tipo_movimento.SAIDA_TRANSFERENCIA_ESTOQUE')) ? $saldo_anterior : $saldo;
        $mov->saldoestoque = $saldo_atual ;			
        Movimento::create(objToArray($mov));
    }
    
    public static function lancarEstoqueDoPdv($id_venda, $tipo_movimento, $descricao, $empresa_id){
        $itens = PdvItemVenda::where("venda_id", $id_venda)->get();
        foreach($itens as $item){
            $ultimoMovimento = Movimento::where(["venda_id"=>$id_venda, "produto_id"=>$item->produto_id])->orderBy('created_at', 'desc')->first();           
            
            $quantidade             = $item->qtde;
            
            $mov                    = new \stdClass();
            $mov->empresa_id        = $empresa_id;
            $mov->tipo_movimento_id = $tipo_movimento;
            $mov->produto_id        = $item->produto_id;
            $mov->pdvvenda_id       = $id_venda;
            $mov->ent_sai           = 'S';
            $mov->estorno           = 'N';
            $mov->data_movimento    = hoje();
            $mov->qtde_movimento    = $quantidade;
            $mov->valor_movimento   = $item->valor;
            $mov->subtotal_movimento= $item->subtotal;
            $mov->descricao         = $descricao;            
            
          
            if(isset($ultimoMovimento->estorno) ){
                if($ultimoMovimento->estorno == "S"){
                    MovimentoService::inserir($mov);
                }
            }else{
                MovimentoService::inserir($mov);
            }
            
            //modifica o status do tipo movimento da grade            
            if($item->produto->usa_grade=="S"){
                $grade = GradeMovimento::where("item_pdvvenda_id", $item->id)->first();
                if($grade){
                    $grade->tipo_movimento_id = $mov->tipo_movimento_id;
                    $grade->save();
                }
            }
            
        }
        PdvVenda::where("id", $id_venda)->update(["estornou_estoque"=>"N"]);
    }
    
    public static function lancarEstoqueDoPedidoDaLoja($id_pedido, $tipo_movimento, $descricao, $empresa_id){
        $itens = LojaItemPedido::where("pedido_id", $id_pedido)->get();
        foreach($itens as $item){
            $ultimoMovimento = Movimento::where(["loja_pedido_id"=>$id_pedido, "produto_id"=>$item->produto_id])->orderBy('created_at', 'desc')->first();
            
            $quantidade             = $item->quantidade;
            
            $mov                    = new \stdClass();
            $mov->empresa_id        = $empresa_id;
            $mov->tipo_movimento_id = $tipo_movimento;
            $mov->produto_id        = $item->produto_id;
            $mov->loja_pedido_id    = $id_pedido;
            $mov->ent_sai           = 'S';
            $mov->estorno           = 'N';
            $mov->data_movimento    = hoje();
            $mov->qtde_movimento    = $quantidade;
            $mov->valor_movimento   = $item->valor;
            $mov->subtotal_movimento= $item->subtotal;
            $mov->descricao         = $descricao;
            
            if($item->produto->usa_grade=="S"){
                    //Insere o movimento de grade
                    $movGrade                       = new \stdClass();
                    $movGrade->tipo_movimento_id    = config("constantes.tipo_movimento.SAIDA_VENDA_LOJA_VIRTUAL");
                    $movGrade->empresa_id           = $empresa_id;
                    $movGrade->produto_id           = $item->produto_id;
                    $movGrade->grade_id             = $item->grade_produto_id;
                    $movGrade->estorno              = 'N';
                    $movGrade->data_movimento       = hoje();
                    $movGrade->qtde_movimento       = $quantidade;
                    $movGrade->item_loja_pedido_id  = $item->id;
                    $movGrade->loja_pedido_id       = $id_pedido;
                    $movGrade->ent_sai              = 'S';
                    $movGrade->descricao            = "Saída Por Venda Loja Virtual - Item: #" . $item->id;
					
            }
          
            if(isset($ultimoMovimento->estorno) ){
                if($ultimoMovimento->estorno == "S"){
                    MovimentoService::inserir($mov);
                    
                    if($movGrade->qtde_movimento > 0){
                        GradeMovimento::Create(objToArray($movGrade));
                    }
                }
            }else{
                MovimentoService::inserir($mov);
                
                if($movGrade->qtde_movimento > 0){
                    GradeMovimento::Create(objToArray($movGrade));
                }
            }
        }
        LojaPedido::where("id", $id_pedido)->update(["estornou_estoque"=>"N"]);
    }
}

