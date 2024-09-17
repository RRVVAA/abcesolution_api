<?php
namespace App\Services;

use App\Http\Resources\DeliveryItemPedidoResource;
use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\CupomDesconto;
use App\Models\Empresa;
use App\Models\EnderecoCliente;
use App\Models\ItemPedidoDelivery;
use App\Models\LojaBanner;
use App\Models\LojaConfiguracao;
use App\Models\LojaImagemProduto;
use App\Models\LojaItemPedido;
use App\Models\LojaPedido;
use App\Models\PedidoDelivery;
use App\Models\Produto;
use App\Models\ProdutoSemelhante;
use App\Models\SubCategoria;


class  DeliveryService
{          
    public static function mostrarNaloja($dados){
  
        $pagina         = $dados->pagina;
        $empresa        = Empresa::where("uuid",$dados->token)->first(); 
       
        $configuracao   = LojaConfiguracao::where("empresa_id",$empresa->id)->first();
        $carrinho       = null;
        $subcategoria   = null;
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
                $carrinho   = PedidoDelivery::where(["empresa_id" =>$empresa->id, "id" => $dados->pedido_id])->first();
                $cupom_desconto = $carrinho->cupom;
                $items      = ItemPedidoDelivery::where("pedido_id", $carrinho->id)->get();
                $itens      = DeliveryItemPedidoResource::collection($items);
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
    
    public static function novoPedido($dados){
        $empresa = Empresa::where("uuid", $dados->empresa_uuid)->first();
    
        $p = new \stdClass();
        $p->cliente_id          = $dados->cliente_id;
        $p->empresa_id          = $empresa->id;
        $p->status_id           = config('constantes.status.DIGITACAO');
        $p->status_financeiro_id= config('constantes.status.DIGITACAO');
        $p->status_entrega_id   = config('constantes.status_entrega.PEDIDO_INICIADO');
        $p->data_pedido         = hoje();
        $p->valor_liquido       = 0;
        $p->valor_venda         = 0;
        $p->desconto_valor      = 0;
        $p->desconto_per        = 0;
        $p->valor_desconto      = 0;
        $p->valor_frete         = 0;
        $cupom = null;
        if($dados->codigo_cupom){
            $cupom = CupomDesconto::where("codigo", $dados->codigo_cupom)->first();
            if($cupom){
                $p->cupom_desconto_id = $cupom->id;
            }
        }
        
        try {
            $pedido = PedidoDelivery::Create(objToArray($p));            
            
        } catch (\Exception $e) {
            return $e->getMessage();
        }
        
        if($pedido){
            if($dados->produto_id){
                try {
                    $it = new \stdClass();
                    $produto = Produto::find($dados->produto_id);
                
                    
                    $it->pedido_id = $pedido->id;
                    $it->produto_id = $produto->id;
                    $it->quantidade = $dados->quantidade;
                    $it->unidade    = $produto->unidade;
                    $it->valor      = $produto->valor_venda;
                    $it->subtotal   = $it->quantidade * $it->valor;
                    
                    $it->desconto_por_valor   = 0;
                    $it->desconto_percentual  = 0;
                    $it->desconto_por_unidade = 0;
                    $it->valor_desconto       = 0;
                    
                    if($cupom){
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
                            }
                        }
                        
                        if($aplicar==true){
                            if($cupom->desconto_percentual > 0){
                                $it->desconto_por_unidade  = $cupom->desconto_percentual * $produto->valor_venda * 0.01;
                            }
                            
                            if($cupom->desconto_por_valor > 0){
                                $it->desconto_por_unidade = $cupom->desconto_por_valor;
                            }
                            
                            $it->cupom_desconto_id = $cupom->id;
                        }
                        
                    }
                    
                    $it->total_desconto_item  =  $it->desconto_por_unidade * $it->quantidade;
                    $it->subtotal_liquido     =  ($produto->valor_venda - $it->desconto_por_unidade )  * $it->quantidade;
                    ItemPedidoDelivery::Create(objToArray($it));
                    
                } catch (\Exception $e) {
                    return $e->getMessage();
                }
                
            }
        }
        return $pedido->id;
    }
  
    
    public static function addItem($dados){
        // $empresa = Empresa::where("uuid", $dados->empresa_uuid)->first();
        
        $pedido = PedidoDelivery::find($dados->pedido_id);
        $cupom = null;
        if($pedido){
            if($pedido->cupom_desconto_id){
                $cupom = $pedido->cupom;
            }
            
            if(!$pedido->cliente_id){
                if($dados->cliente_id){
                    $pedido->cliente_id = $dados->cliente_id;
                    $pedido->save;
                }
            }
            if($dados->produto_id){
                $it = new \stdClass();
                $it->grade_produto_id = null;
                $produto = Produto::find($dados->produto_id);
               /* if($produto->usa_grade =="S"){
                    $grade = GradeProduto::where(["produto_id"=>$produto->id,"linha_id"=>$dados->linha_id,"coluna_id"=>$dados->coluna_id])->first();
                    $it->grade_produto_id = $grade->id ?? null;
                }
                */
                $it->pedido_id  = $pedido->id;
                $it->produto_id = $produto->id;
                $it->quantidade = $dados->quantidade;
                $it->valor      = $produto->valor_venda;
                $it->subtotal   = $it->quantidade * $it->valor;
                
                
                
                $it->desconto_por_valor   = 0;
                $it->desconto_percentual  = 0;
                $it->desconto_por_unidade = 0;
                $it->valor_desconto       = 0;
                
                if($cupom){
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
                        }
                    }
                    
                    if($aplicar==true){
                        if($cupom->desconto_percentual > 0){
                            $it->desconto_por_unidade  = $cupom->desconto_percentual * $produto->valor_venda * 0.01;
                        }
                        
                        if($cupom->desconto_por_valor > 0){
                            $it->desconto_por_unidade = $cupom->desconto_por_valor;
                        }
                        
                        $it->cupom_desconto_id = $cupom->id;
                        
                    }
                    
                }
                
                
                $it->total_desconto_item  =  $it->desconto_por_unidade * $it->quantidade;
                $it->subtotal_liquido     =  ($produto->valor_venda - $it->desconto_por_unidade )  * $it->quantidade;
                $item = ItemPedidoDelivery::Create(objToArray($it));
            }
        }
        return $item->id ?? null;
    }
    
    public static function novoCliente($dados){
        $empresa = Empresa::where("uuid", $dados->empresa_uuid)->first();
        $cli = new \stdClass();
        $cli->empresa_id        = $empresa->id;
        $cli->nome_razao_social = $dados->nome;
        $cli->nome_fantasia     = $dados->nome;
        $cli->cpf_cnpj          = $dados->cpf;
        $cli->tipo_cliente      = "F";
        $cli->tipo_contribuinte = 9;
        $cli->email             = $dados->email;
        $cli->telefone          = $dados->telefone;
        $cli->senha             = $dados->senha;
        $cli->indFinal          = config("constanteNota.indFinal.CONSUMIDOR_FINAL");;
        $cli->password          = md5($dados->senha);
        $cli->status_id         = config("constantes.status.ATIVO");
        
        $cli->logradouro        = $dados->rua ;
        $cli->numero            = $dados->numero;
        $cli->bairro            = $dados->bairro;
        $cli->cep               = $dados->cep;
        $cli->complemento       = $dados->complemento;
        $cli->cidade            = $dados->cidade;
        $cli->uf                = $dados->uf;
        $cli->ibge              = $dados->ibge;
        $cliente                = Cliente::where("email", $cli->email)->first();
        if(!$cliente){
            $cliente            = Cliente::Create(objToArray($cli));
        }
        
        //Setando o cliente no pedido
        if($cliente){
            $end                    = new \stdClass();
            $end->empresa_id        = $empresa->id;
            $end->cliente_id        = $cliente->id;
            $end->logradouro        = $dados->rua;
            $end->numero            = $dados->numero;
            $end->bairro            = $dados->bairro;
            $end->cep               = $dados->cep;
            $end->complemento       = $dados->complemento;
            $end->cidade            = $dados->cidade;
            $end->uf                = $dados->uf;
            $end->ibge              = $dados->ibge;
            $endereco               = EnderecoCliente::Create(objToArray($end));
            if($dados->pedido_id){
               PedidoDelivery::find($dados->pedido_id)->update(["cliente_id" =>$cliente->id, "endereco_id"=>$endereco->id]);
            }
        }
        return $cliente->id;
    }
}

