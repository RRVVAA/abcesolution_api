<?php
namespace App\Services;


use App\Http\Resources\PdvVendaResource;
use App\Models\CupomDesconto;
use App\Models\GradeProduto;
use App\Models\PdvCaixa;
use App\Models\PdvDuplicata;
use App\Models\PdvItemVenda;
use App\Models\PdvVenda;
use App\Models\Produto;
use App\Models\User;

class PdvService
{
    public static function mostrarNoPdv($dados){
        $acao           = $dados->acao;        
        $usuario        = User::where("uuid",$dados->usuario_uuid)->first();        
        $venda          = null;
        $caixa          = null;
        
        if($acao=="verificar_venda_aberta" ){
            $venda          = PdvVenda::where("usuario_id", $usuario->id)->where("caixa_id", $dados->caixa_id)->where("status_id", config("constantes.status.DIGITACAO"))->first();
            if($venda){
                return new PdvVendaResource($venda);
            }
                        
        }
        
        if($acao=="ver_caixa" ){
            $caixa          = PdvCaixa::where("id",$dados->caixa_id)->with("status")->first();           
        }
        
        $retorno                = new \stdClass();
        $retorno->venda         = $venda;
        $retorno->caixa         = $caixa;
        return  $retorno;
    }
    
    public static function  inserirItem($dados){
        $retorno    = new \stdClass();
        $produto    = null;
        $grade_id   = $dados->grade_id ?? null;
        $venda_id   = $dados->venda_id ?? null;
        $retorno->eh_grade = false;        
        
        //pesquisa pela grade
        if($grade_id!=null){
            $grade               = GradeProduto::where("id", $grade_id)->where("empresa_id", $dados->empresa_id)->first();
            if($grade){
                $grade_id            = $grade->id;
                $produto             = $grade->produto;
            }            
        }
        
        //Pesquisa pelo Còdigo de Barra
        if(!$produto){
            $produto               = Produto::where("codigo_barra", $dados->q)->where("empresa_id", $dados->empresa_id)->first();
        }
        //Pesquisa pelo id
        if(!$produto){
            $produto           = Produto::where(["id" => $dados->q, "empresa_id"=>$dados->empresa_id])->first();
        }
        
        //Se não achou procura na tabela de grade
        if(!$produto){
            $grade               = GradeProduto::where("codigo_barra", $dados->q)->where("empresa_id", $dados->empresa_id)->first();
            if($grade){
                $grade_id            = $grade->id;
                $produto             = $grade->produto;
            }            
        }
        
        //Pesquisa pelo sku
        if(!$produto){
            $produto           = Produto::where(["sku" => $dados->q, "empresa_id"=>$dados->empresa_id])->first();
        }
        
        //Se mesmo assim não achou então retorna null
        if(!$produto){
            $retorno->eh_grade = false;
            $retorno->venda_id = null;
            return $retorno;
        }
        
        
        //Verifica se o produto é grade - retorna para a montagem da grade
        if($produto->usa_grade=="S" && $grade_id==null){            
            if($produto->estoque->quantidade != $produto->estoque->qtde_grade){
                $retorno->eh_grade = true;
                $retorno->venda_id = null;
                $retorno->produto_id = null;
                return $retorno;
            }else{
                $retorno->eh_grade = true;
                $retorno->produto_id = $produto->id;
                return $retorno;
            }            
            
        }     
         
        
        //Verificar se a venda existe
        if(!$venda_id){
            //Veririfica se existe cupom
            $cupom_id = null;
            if($dados->codigo_cupom){
                $cupom      = CupomDesconto::where("codigo", $dados->codigo_cupom)->first(); 
                $cupom_id   = $cupom->id;
            }
            
            $novaVenda = PdvVendaService::gerarVenda($dados->usuario_uuid, $cupom_id); 
            $venda_id = $novaVenda->id;
            if(!$venda_id){
                $retorno->eh_grade = false;
                $retorno->venda_id = null;
                $retorno->erro = "Não foi possível salvar a venda";
                return $retorno;
            }            
            $venda = PdvVenda::find($novaVenda->id);
        }else{
            $venda = PdvVenda::find($venda_id);
        }
        
        
        $item                       = new \stdClass();
        $item->venda_id      	    = $venda_id;
        $item->produto_id    	    = $produto->id;
        $item->grade_produto_id     = $grade_id;        
        $item->qtde          	    = $dados->qtde;
        $item->valor         	    = $produto->valor_venda;
        $item->subtotal             = $item->valor * $item->qtde;
        $item->desconto_por_valor   = $dados->desconto_por_valor;
        $item->desconto_percentual  = $dados->desconto_percentual;
        $item->desconto_por_unidade = 0;
        
        if($item->desconto_por_valor > 0){
            $item->desconto_por_unidade = $item->desconto_por_valor;
        }
        if($item->desconto_percentual > 0){
            $item->desconto_por_unidade = $item->desconto_percentual * $item->valor * 0.01;
        }        
        
        
        //Aplicar o cupom fiscal
        if($venda->cupom_desconto_id){
            $cupom      = CupomDesconto::find($venda->cupom_desconto_id); 
            $validadeCupom = CupomDescontoService::validarCupom($cupom, $produto) ;
            if($validadeCupom){   
                
                if($cupom->desconto_percentual > 0){
                    $item->desconto_por_unidade  = $cupom->desconto_percentual * $item->valor * 0.01;
                }
                
                if($cupom->desconto_por_valor > 0){
                    $item->desconto_por_unidade = $cupom->desconto_por_valor;
                }
                
                $item->cupom_desconto_id = $cupom->id;
            }            
        }
        
        $item->subtotal_liquido    =  ($item->valor - $item->desconto_por_unidade )  * $item->qtde;
        $item->total_desconto_item  =  $item->desconto_por_unidade  * $item->qtde;
        $item = PdvItemVenda::Create(objToArray($item));
        
        $retorno->eh_grade = false;        
        $retorno->venda_id = $item->venda_id;
        
        return $retorno;
        
    }
        
       
    
    public static function abrirCaixa($dados){
        $usuario = User::where("uuid", $dados->usuario_abriu_uuid)->first();
       
        $caixa   = null;
        if($usuario){
            $caixa = PdvCaixa::where("usuario_abriu_id", $usuario->id)->where("status_id", config("constantes.status.ABERTO"))->first();
            if(!$caixa){
                $cx = new \stdClass();
                $cx->data_abertura 	      = hoje();
                $cx->hora_abertura 	      = agora();
                $cx->empresa_id           = $usuario->empresa_id;
                $cx->usuario_abriu_id     = $usuario->id;
                $cx->caixanumero_id       = $dados->caixa_numero_id ?? 0 ;
                $cx->valor_abertura       = $dados->valor_abertura ?? 0 ;
                $cx->valor_fechamento     = $dados->valor_fechamento ?? 0 ;
                $cx->valor_vendido        = $dados->valor_vendido ?? 0 ;
                $cx->valor_quebra         = $dados->valor_quebra ??  0 ;
                $cx->valor_sangria        = $dados->valor_sangria ?? 0 ;
                $cx->valor_suplemento     = $dados->valor_suplemento ??  0 ;
                $cx->total_em_caixa       = $dados->total_em_caixa ?? 0 ;
                $cx->status_id            = config("constantes.status.ABERTO")  ;
                $caixa                    = PdvCaixa::Create(objToArray($cx));
            }
        }
        return $usuario;
        
    }
    
    public static function fecharCaixa($dados){
        $usuario = User::where("uuid", $dados->usuario_abriu_uuid)->first();
        if($usuario){
            $caixa = PdvCaixa::where("usuario_abriu_id", $usuario->id)->where("status_id", config("constantes.status.ABERTO"))->first();
            if($caixa){
                $caixa->data_fechamento     = hoje();
                $caixa->hora_fechamento     = agora();
                $caixa->valor_fechamento    = $caixa->total_em_caixa;
                $caixa->usuario_fechou_id   = $usuario->id;
                $caixa->status_id           = config("constantes.status.FECHADO")  ;
                $caixa->save();
            }            
        }
        
        return $usuario;
    }
    
    public static function vincularCliente($dados){       
        $venda = PdvVenda::find($dados->venda_id);
        $venda->cliente_id = $dados->cliente_id;
        $venda->save();
        return $venda;        
    }
    
    public static function gerarCrediario($dados){
        $venda            = PdvVenda::find($dados->venda_id);
        if($venda){
            $tPag             = 5;
            
            if($dados->forma_de_parcelar_crediario==2){
                $array_origem  = explode("-", $dados->qtde_parcela_crediario);
            }else{
                for($i=1; $i<=$dados->qtde_parcela_crediario; $i++){
                    $array_origem[] = 30 * $i;
                }
            }
            $array_parcela = array();
            for($i=0; $i <count($array_origem);$i++){
                if(is_numeric($array_origem[$i])){
                    $array_parcela[] = $array_origem[$i];
                }
            }
            
            $valor_total    = $dados->valor;
            $qtde_parcela   = count($array_parcela);
            $parcela        = number_format($valor_total / $qtde_parcela, 2, ".", "");
            
            
            $soma_parcela          = 0;
            
            for($i=0; $i < $qtde_parcela; $i++){
                $pag            = new \stdClass();
                $pag->venda_id  = $venda->id;
                $pag->caixa_id  = $venda->caixa_id;
                $pag->tPag      = $tPag;
                $pag->nDup      = zeroEsquerda($i+1, 3);
                $pag->dVenc     = somarData($dados->data_pagamento_parcela, $array_parcela[$i]);
                
                if($i == $qtde_parcela-1){
                    $valor_parcela = $valor_total - $soma_parcela;
                }else{
                    $valor_parcela = $parcela;
                }
                
                $soma_parcela += $parcela;
                $pag->vDup = $valor_parcela;
                $total = PdvDuplicata::where("venda_id", $pag->venda_id)->sum("vDup");
                $restante = $venda->valor_liquido - $total;
                if($restante > 0){
                    PdvDuplicata::Create(objToArray($pag));
                }
                
                
            } 
        }
               
        return $venda;
    }
    
    public static function gerarPagamentoCartao($dados){
        $venda            = PdvVenda::find($dados->venda_id);
        $tPag             = 3;
        $valor_total      = $dados->valor;
        $qtde_parcela     = $dados->qtde_parcela_cartao;
        $parcela          = number_format($valor_total / $qtde_parcela, 2, ".", "");               
        
        $soma_parcela          = 0;
        
        for($i=0; $i < $qtde_parcela; $i++){
            $pag            = new \stdClass();
            $pag->venda_id  = $venda->id;
            $pag->caixa_id  = $venda->caixa_id;
            $pag->tPag      = $tPag;
            $pag->nDup      = zeroEsquerda($i+1, 3);
            $pag->dVenc     = somarData(hoje(), $i * 30);
            
            if($i == $qtde_parcela-1){
                $valor_parcela = $valor_total - $soma_parcela;
            }else{
                $valor_parcela = $parcela;
            }
            
            $soma_parcela += $parcela;
            $pag->vDup = $valor_parcela;
            $total = PdvDuplicata::where("venda_id", $pag->venda_id)->sum("vDup");
            $restante = $venda->valor_liquido - $total;
            if($restante > 0){
                PdvDuplicata::Create(objToArray($pag));
            }            
        }
        return $venda;
    }
    
    public static function armazenarVenda($dados){  
       
        $pdvVenda = PdvVenda::find($dados->venda_id);       
        //exclui todos o pagamentos
        $duplicatas = PdvDuplicata::where("venda_id", $dados->venda_id)->get();
        foreach($duplicatas as $dup){
            $dup->delete();
        }
        
        $pdvVenda->status_id    = config("constantes.status.PDVVENDA_SALVA");
        $pdvVenda->caixa_id     = null;
        $pdvVenda->cliente_id   = null;
        $pdvVenda->titulo       = $dados->titulo_venda;
        $pdvVenda->save();
        return $pdvVenda;
        
    }
}

