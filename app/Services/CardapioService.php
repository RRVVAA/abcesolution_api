<?php
namespace App\Services;

use App\Http\Resources\LojaItemPedidoResource;
use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\EnderecoCliente;
use App\Models\LojaBanner;
use App\Models\LojaConfiguracao;
use App\Models\LojaImagemProduto;
use App\Models\LojaItemPedido;
use App\Models\LojaPedido;
use App\Models\Produto;
use App\Models\ProdutoSemelhante;
use App\Models\SubCategoria;


class  CardapioService
{          
    public static function mostrarNaloja($dados){
  
        $pagina         = $dados->pagina;
        $empresa        = Empresa::where("uuid",$dados->token)->first(); 
       
        $configuracao   = LojaConfiguracao::where("empresa_id",$empresa->id)->first();
        $carrinho       = null;
        $subcategoria  = null;
        $itens          = array(); 
        $produtos       = array();
        $categorias     = array();
        $lista_banner   = array();
        $cliente        = null;
        $enderecos      = array();
        $pedidos        = array();  
        $semelhantes    = array();
        $produto        = null;
        $pedido         = null;
        $endereco       = null;
        $imagens        = array();
        $grade          = null;
        $cupom_desconto = null;
        
        if($pagina=="home" ){
            $produtos       = Produto::where(["empresa_id" =>$empresa->id, "produto_loja" => "S"])->get();
        }
        
        if($pagina=="subcategoria" ){
            if($dados->subcategoria_id){
                $subcategoria   = SubCategoria::find($dados->subcategoria_id) ;
                $produtos = Produto::where(["subcategoria_id"=>$dados->subcategoria_id, "produto_loja" => "S"])->get();
            }
                    
        }
       
        if($pagina=="detalhe_produto"){
            $produto        = Produto::where("id", $dados->url_produto)->with('categoria')->first();            
            $semelhantes    = ProdutoSemelhante::lista($produto->id);
            $imagens        = LojaImagemProduto::where("produto_id", $produto->id)->get();
            if($produto->usa_grade=="S"){
                $grade = GradeService::montar($produto->id);
            }
            
        }
        
        if($pagina=="pesquisa"){
            $produtos       = Produto::where("produto_loja","S")->where("nome","like", "%$dados->q%")->get();
        }        
        
        if($pagina=="home" || $pagina=="subcategoria" || $pagina=="pesquisa"){
            $categorias     = Categoria::where(["empresa_id" =>$empresa->id])->with('subcategorias')->get();
        }
        
        if($pagina=="home"){
            $lista_banner   = LojaBanner::where(["empresa_id" =>$empresa->id, "status_id"=>config("constantes.status.ATIVO")])->get();
        }
        
        if($pagina=="pedido"  || $pagina=="escolher_pagamento" ){
            $pedido   = LojaPedido::where(["empresa_id" =>$empresa->id, "cliente_id" => $dados->cliente_id, "uuid"=>$dados->uuid_pedido])->first();
        }
        
        if( $pagina=="finalizado"){
            $pedido   = LojaPedido::where(["empresa_id" =>$empresa->id,  "uuid"=>$dados->uuid_pedido])->first();
        }
        
        if( $pagina=="acompanhar"){
            $pedido   = LojaPedido::where(["empresa_id" =>$empresa->id,  "uuid"=>$dados->uuid_pedido])->with('forma_pagamento')->with('status_entrega')->first();
            $itens    = LojaItemPedido::where("pedido_id", $pedido->id)->with('produto')->get();
            if($pedido->cliente_id){
                $cliente        = Cliente::find($pedido->cliente_id);
                if($pedido->endereco_id){
                    $endereco     = EnderecoCliente::find($pedido->endereco_id);
                }
                
            }
        }
        
        if($dados->pedido_id ){
            if($pagina=="home" || $pagina=="carrinho" || $pagina=="checkout" || $pagina=="endereco_carrinho" || $pagina=="pedido" || $pagina=="escolher_pagamento"){
                $carrinho   = LojaPedido::where(["empresa_id" =>$empresa->id, "id" => $dados->pedido_id])->first();
                $cupom_desconto = $carrinho->cupom;
                $items      = LojaItemPedido::where("pedido_id", $carrinho->id)->get();
                $itens      = LojaItemPedidoResource::collection($items);
                if($carrinho->cliente_id){
                    if($pagina=="escolher_pagamento"){                        
                        $cliente        = Cliente::find($carrinho->cliente_id);
                        if($carrinho->endereco_id){
                            $endereco     = EnderecoCliente::find($carrinho->endereco_id);
                        }                        
                    }
                }
            }
        }
        
        if($dados->cliente_id){
            if($pagina=="endereco_carrinho"){
                $cliente        = Cliente::find($dados->cliente_id);
                if($cliente){
                     $enderecos      = EnderecoCliente::where("cliente_id", $dados->cliente_id)->get();
                }
                
            }
        }
          
        $retorno                = new \stdClass();
        $retorno->empresa       = $empresa;
        $retorno->configuracao  = $configuracao;
        $retorno->carrinho      = $carrinho;
        $retorno->cupom_desconto= $cupom_desconto;
        $retorno->itens         = $itens ;
        $retorno->produtos      = $produtos;
        $retorno->categorias    = $categorias;
        $retorno->subcategoria  = $subcategoria;
        $retorno->lista_banner  = $lista_banner;
        $retorno->cliente       = $cliente;
        $retorno->enderecos     = $enderecos;
        $retorno->pedidos       = $pedidos; 
        $retorno->produto       = $produto;
        $retorno->pedido        = $pedido;
        $retorno->endereco      = $endereco;
        $retorno->semelhantes   = $semelhantes;
        $retorno->imagens       = $imagens;
        $retorno->grade         = $grade;
        return  $retorno;
    }
    
    
   
}

