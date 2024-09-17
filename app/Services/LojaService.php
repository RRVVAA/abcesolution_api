<?php
namespace App\Services;

use App\Http\Resources\LojaItemPedidoResource;
use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\Cobranca;
use App\Models\CupomDesconto;
use App\Models\Emitente;
use App\Models\Empresa;
use App\Models\EnderecoCliente;
use App\Models\FinFatura;
use App\Models\GradeProduto;
use App\Models\LojaBanner;
use App\Models\LojaConfiguracao;
use App\Models\LojaImagemProduto;
use App\Models\LojaItemPedido;
use App\Models\LojaPedido;
use App\Models\PdvVenda;
use App\Models\Produto;
use App\Models\ProdutoSemelhante;
use App\Models\SubCategoria;


class  LojaService
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


    public static function getEnderecoPeloId( $id){
        return       EnderecoCliente::find($id);
    }

    //Confirma o pagamento vindo do webhook
    public static function confirmarPagamentoPdv($dados){
        $pdvVenda = PdvVenda::find($dados->id);
        $contaReceber   = ContaReceberSevice::inserirPeloPdvDuplicata($pdvVenda);
        if($contaReceber){
            RecebimentoService::inserirPelaLojaPedido($contaReceber);
        }

        $tipo_movimento         = config("constantes.tipo_movimento.SAIDA_VENDA_LOJA_VIRTUAL");
        $descricao              = "Saida Loja Virutal - Pedido: #" . $pdvVenda->id;
        MovimentoService::lancarEstoqueDoPedidoDaLoja($pdvVenda->id, $tipo_movimento, $descricao, $pdvVenda->empresa_id);

        $pdvVenda->status_id  = config("constantes.status.FINALIZADO");;
        $pdvVenda->save();
    }

    public static function confirmarPagamentoPedidoLoja($dados){
        $lojaPedido = LojaPedido::find($dados->id);
        $contaReceber   = ContaReceberSevice::inserirPeloPedidoDaLoja($lojaPedido);
        if($contaReceber){
            RecebimentoService::inserirPelaLojaPedido($contaReceber);
        }

        $tipo_movimento         = config("constantes.tipo_movimento.SAIDA_VENDA_LOJA_VIRTUAL");
        $descricao              = "Saida Loja Virutal - Pedido: #" . $lojaPedido->id;
        MovimentoService::lancarEstoqueDoPedidoDaLoja($lojaPedido->id, $tipo_movimento, $descricao, $lojaPedido->empresa_id);

        $lojaPedido->status_id  = config("constantes.status.FINALIZADO");;
        $lojaPedido->save();
    }

    public static function confirmarPagamentoFatura($dados){
        $fatura = FinFatura::find($dados->id);
        $contaReceber   = ContaReceberSevice::inserirPelaFatura($fatura);
        if($contaReceber){
            RecebimentoService::inserirPelaLojaPedido($contaReceber);
        }

        $tipo_movimento         = config("constantes.tipo_movimento.SAIDA_VENDA_LOJA_VIRTUAL");
        $descricao              = "Saida Loja Virutal - Pedido: #" . $fatura->id;
        MovimentoService::lancarEstoqueDoPedidoDaLoja($fatura->id, $tipo_movimento, $descricao, $fatura->empresa_id);

        $fatura->status_id  = config("constantes.status.FINALIZADO");;
        $fatura->save();
    }

    public static function confirmarPagamentoCobranca($dados){
        $cobranca = Cobranca::find($dados->id);
        $contaReceber   = ContaReceberSevice::inserirPelaCobracan($cobranca);
        if($contaReceber){
            RecebimentoService::inserirPelaLojaPedido($contaReceber);
        }

        $tipo_movimento         = config("constantes.tipo_movimento.SAIDA_VENDA_LOJA_VIRTUAL");
        $descricao              = "Saida Loja Virutal - Pedido: #" . $cobranca->id;
        MovimentoService::lancarEstoqueDoPedidoDaLoja($cobranca->id, $tipo_movimento, $descricao, $cobranca->empresa_id);

        $cobranca->status_id  = config("constantes.status.FINALIZADO");;
        $cobranca->save();
    }



    public static function finalizarPedido($dados){

        $pedido         = LojaPedido::where("uuid", $dados->uuid_pedido)->first();
        $emitente       = Emitente::where("empresa_id", $pedido->empresa_id)->first();

        if($emitente){
            if(!$emitente->loja_conta_corrente_id){
                $retorno = new \stdClass();
                $retorno->tem_erro = true;
                $retorno->erro = "Não foi possível finalizar o Pedido, é necessário ter uma conta corrente padrão configurada.";
                return $retorno;
            }
        }

        $contaReceber   = ContaReceberSevice::inserirPeloPedidoDaLoja($pedido);
        if($contaReceber){
            $pedido->forma_pagto_id  = $dados->forma_pagto;
            RecebimentoService::inserirPelaLojaPedido($contaReceber, $dados->forma_pagto);

            $tipo_movimento         = config("constantes.tipo_movimento.SAIDA_VENDA_LOJA_VIRTUAL");
            $descricao              = "Saida Loja Virtual - Pedido: #" . $pedido->id;
            MovimentoService::lancarEstoqueDoPedidoDaLoja($pedido->id, $tipo_movimento, $descricao, $pedido->empresa_id);
            $pedido->data_pagamento         = hoje();
            $pedido->status_id              = config("constantes.status.FINALIZADO");
            $pedido->status_financeiro_id   = config("constantes.status.PAGO");
            $pedido->status_entrega_id      = config("constantes.status_entrega.PAGAMENTO_CONFIRMADO");
            $pedido->save();
        }

        return $pedido;
    }


    public static function retormarPedidoDaLojaPeloPdv($dados){
        $pdvVenda   = PdvVenda::where("uuid", $dados->uuid_pdv)->first();
        $pedido     = null;
        $id         = null;
        if($pdvVenda){
            //se tiver cliente
      /*      if($dados->cliente_id){
                $pedido = LojaPedido::where(["pdvvenda_id" => $pdvVenda->id, "cliente_id" => $dados->cliente_id])->first();
            }

            if($pedido){
                return $pedido;
            }
            */
            //Criar o pedido na loja virtual
            $pedido = new \stdClass();
            $pedido->empresa_id     = $pdvVenda->empresa_id;
            $pedido->status_id      = config('constantes.status.DIGITACAO');
            $pedido->cliente_id     = $dados->cliente_id;
            $pedido->valor_liquido  = $pdvVenda->valor_liquido;
            $pedido->valor_venda    = $pdvVenda->valor_venda;
            $pedido->valor_frete    = $pdvVenda->valor_frete;
            $pedido->desconto_valor = $pdvVenda->desconto_valor;
            $pedido->desconto_per   = $pdvVenda->desconto_per;
            $pedido->valor_desconto = $pdvVenda->valor_desconto;
            $pedido->data_pedido    = hoje();
            $pedido->pdvvenda_id    = $pdvVenda->id;
            $pedido = LojaPedido::Create(objToArray($pedido));

            if($pedido){
                $itens = $pdvVenda->itens;
                foreach($itens as $i){
                    $item               = new \stdClass();
                    $item->pedido_id    = $pedido->id;
                    $item->produto_id   = $i->produto_id;
                    $item->quantidade   = $i->qtde;
                    $item->valor        = $i->valor;
                    $item->subtotal     = $i->subtotal;
                    $item->subtotal_liquido     = $i->subtotal_liquido;
                    $item->desconto_percentual  = $i->desconto_percentual;
                    $item->desconto_por_valor   = $i->desconto_por_valor;
                    $item->desconto_por_unidade = $i->desconto_por_unidade;
                    $item->total_desconto_item  = $i->total_desconto_item;
                    LojaItemPedido::Create(objToArray($item));
                }
            }
            return $pedido;
        }
        return $pedido;
    }


    public static function atualizarItem($dados){
        $empresa = Empresa::where("uuid", $dados->token)->first();

        if($empresa){
            $item                       = LojaItemPedido::where("id",$dados->id)->first();
            $item->quantidade           = $dados->qtde;
            $item->subtotal             = $item->quantidade * $item->valor;


            $item->subtotal_liquido  =  ($item->valor - $item->desconto_por_unidade )  * $item->quantidade;
            $item->total_desconto_item  =  $item->desconto_por_unidade  * $item->quantidade;

            $item->save();
            LojaPedido::somarTotal($item->pedido_id);

        }

        return  true ;

    }


    public static function excluirItem($dados){
        $empresa = Empresa::where("uuid", $dados->token)->first();
        if($empresa){
            $item = LojaItemPedido::find($dados->id);
            $item->delete();
        }

        return  true ;
    }





















    public static function home($dados){
        $empresa        = Empresa::where("uuid",$dados->token)->first();
        $configuracao   = LojaConfiguracao::where("empresa_id",$empresa->id)->first();
        $produtos       = Produto::where(["empresa_id" =>$empresa->id, "produto_loja" => "S"])->get();
        $categorias     = Categoria::where(["empresa_id" =>$empresa->id])->with('subcategorias')->get();
        $lista_banner   = LojaBanner::where(["empresa_id" =>$empresa->id, "status_id"=>config("constantes.status.ATIVO")])->get();
        $carrinho       = null;
        $itens          = array();
        if($dados->pedido_id){
            $carrinho   = LojaPedido::where(["empresa_id" =>$empresa->id, "id" => $dados->pedido_id])->first();
            $itens      = LojaItemPedido::where("pedido_id", $carrinho->id)->with('produto')->get();
        }

        $retorno                = new \stdClass();
        $retorno->empresa       = $empresa;
        $retorno->configuracao  = $configuracao;
        $retorno->carrinho      = $carrinho;
        $retorno->itens         = $itens ;
        $retorno->produtos      = $produtos;
        $retorno->categorias    = $categorias;
        $retorno->lista_banner  = $lista_banner;
        return  $retorno;
    }

    public static function categoria(string $uuid, $categoria_id){
        $empresa        = Empresa::where("uuid",$uuid)->first();
        $configuracao   = LojaConfiguracao::where("empresa_id",$empresa->id)->first();
        $categoria      = Categoria::find($categoria_id);
        $produtos       = Produto::where("categoria_id",$categoria_id)->get();
        $categorias     = Categoria::where(["empresa_id" =>$empresa->id])->with('subcategorias')->get();
        $carrinho       = null;
        $itens          = array();

        if($dados->pedido_id){
            $carrinho   = LojaPedido::where(["empresa_id" =>$empresa->id, "id" => $dados->pedido_id])->first();
            $itens      = LojaItemPedido::where("pedido_id", $carrinho->id)->with('produto')->get();
        }

        $retorno                = new \stdClass();
        $retorno->empresa       = $empresa;
        $retorno->configuracao  = $configuracao;
        $retorno->carrinho      = $carrinho;
        $retorno->itens         = $itens ;
        $retorno->categoria     = $categoria;
        $retorno->produtos      = $produtos;
        $retorno->categorias    = $categorias;
        return  $retorno;
    }

    public static function subcategoria(string $uuid, $subcategoria_id){
        $empresa        = Empresa::where("uuid",$uuid)->first();
        $configuracao   = LojaConfiguracao::where("empresa_id",$empresa->id)->first();
        $subcategoria   = SubCategoria::find($subcategoria_id) ;
        $produtos       = Produto::where("subcategoria_id",$subcategoria_id)->get();
        $categorias     = Categoria::where(["empresa_id" =>$empresa->id])->with('subcategorias')->get();

        $retorno                = new \stdClass();
        $retorno->empresa       = $empresa;
        $retorno->configuracao  = $configuracao;
        $retorno->subcategoria  = $subcategoria;
        $retorno->produtos      = $produtos;
        $retorno->categorias    = $categorias;
        return  $retorno;
    }
    public static function pesquisa(string $uuid, $q){
        $empresa        = Empresa::where("uuid",$uuid)->first();
        $configuracao   = LojaConfiguracao::where("empresa_id",$empresa->id)->first();
        $produtos       = Produto::where("nome","like", "%$q%")->get();
        $categorias     = Categoria::where(["empresa_id" =>$empresa->id])->with('subcategorias')->get();

        $retorno                = new \stdClass();
        $retorno->empresa       = $empresa;
        $retorno->configuracao  = $configuracao;
        $retorno->produtos      = $produtos;
        $retorno->categorias    = $categorias;
        return  $retorno;
    }

    public static function detalhe(string $valor){
        $produto        = Produto::where("id", $valor)->with('categoria')->first();
        $configuracao   = LojaConfiguracao::where("empresa_id",$produto->empresa_id)->first();
        $categorias     = Categoria::where(["empresa_id" =>$produto->empresa_id])->with('subcategorias')->get();

        $retorno                = new \stdClass();
        $retorno->produto       = $produto;
        $retorno->configuracao  = $configuracao;
        $retorno->categorias    = $categorias;
        return  $retorno;
    }

    public static function getPedidoPeloUuid(string $uuid){
        $pedido             = LojaPedido::where("uuid", $uuid)->with("cliente")->with("empresa.parametro")->with("empresa.lojaConfiguracao")->first();
        return $pedido;
    }



    public static function carrinho($dados){

        $empresa        = Empresa::where("uuid",$dados->empresa_uuid)->first();

        $configuracao   = LojaConfiguracao::where("empresa_id",$empresa->id)->first();

        $carrinho       = null;
        $itens          = array();
        $cliente        = null;
        $enderecos      = array();




        if($dados->pedido_id){
            $carrinho       = LojaPedido::where(["empresa_id" =>$empresa->id, "id" => $dados->pedido_id])->first();
            $itens          = LojaItemPedido::where("pedido_id" , $dados->pedido_id)->with('produto')->get();
            $cliente        = Cliente::find($carrinho->cliente_id);
            $enderecos      = EnderecoCliente::where("cliente_id", $carrinho->cliente_id)->get();
        }


        $retorno              = new \stdClass();
        $retorno->configuracao= $configuracao;
        $retorno->carrinho    = $carrinho;
        $retorno->itens       = $itens;
        $retorno->cliente     = $cliente;
        $retorno->enderecos   = $enderecos;
        return  $retorno;
    }

    public static function perfil($dados){
        $empresa        = Empresa::where("uuid",$dados->empresa_uuid)->first();
        $configuracao   = LojaConfiguracao::where("empresa_id",$empresa->id)->first();
        $carrinho       = null;
        $itens          = array();
        $cliente        = null;
        $enderecos      = array();
        $pedidos        = array();

        if($dados->cliente_id){
            $cliente        = Cliente::find($dados->cliente_id);
            $enderecos      = EnderecoCliente::where("cliente_id", $dados->cliente_id)->get();
            $pedidos        = LojaPedido::where("cliente_id", $dados->cliente_id)->get();
        }

        if($dados->pedido_id){
            $carrinho       = LojaPedido::where(["empresa_id" =>$empresa->id, "id" => $dados->pedido_id])->first();
            $itens          = LojaItemPedido::where("pedido_id" , $dados->pedido_id)->with('produto')->get();
            $cliente        = Cliente::find($carrinho->cliente_id);
            $enderecos      = EnderecoCliente::where("cliente_id", $carrinho->cliente_id)->get();
        }


        $retorno              = new \stdClass();
        $retorno->configuracao= $configuracao;
        $retorno->carrinho    = $carrinho;
        $retorno->itens       = $itens;
        $retorno->cliente     = $cliente;
        $retorno->enderecos   = $enderecos;
        $retorno->pedidos     = $pedidos;
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
        $p->tipo_frete          = '';
        $p->numero_nfe          = 0;
        $p->observacao          = '';
        $cupom = null;
        if($dados->codigo_cupom){
            $cupom = CupomDesconto::where("codigo", $dados->codigo_cupom)->first();
            if($cupom){
                $p->cupom_desconto_id = $cupom->id;
            }
        }

        try {
            $pedido =LojaPedido::Create(objToArray($p));
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        if($pedido){
            if($dados->produto_id){
                try {
                    $it = new \stdClass();
                    $produto = Produto::find($dados->produto_id);
                    if($produto->usa_grade =="S"){
                        $grade = GradeProduto::where(["produto_id"=>$produto->id,"linha_id"=>$dados->linha_id,"coluna_id"=>$dados->coluna_id])->first();
                        $it->grade_produto_id = $grade->id ?? null;
                    }


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
                    LojaItemPedido::Create(objToArray($it));

                } catch (\Exception $e) {
                    return $e->getMessage();
                }

            }
        }
        return $pedido->id;
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
        $cli->senha             = $dados->senha;
        $cli->indFinal          = config("constanteNota.indFinal.CONSUMIDOR_FINAL");;
        $cli->password          = md5($dados->senha);
        $cli->status_id         = config("constantes.status.ATIVO");

        $cli->logradouro        = $dados->rua;
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
                LojaPedido::find($dados->pedido_id)->update(["cliente_id" =>$cliente->id, "endereco_id"=>$endereco->id]);
            }
        }
        return $cliente->id;
    }

    public static function salvarEnderecoCliente($dados){
        $cliente = Cliente::find($dados->cliente_id);
        if($cliente){
            if($dados->id){
                return EnderecoCliente::find($dados->id)->update(objToArray($dados));
            }else{
                $dados->empresa_id = $cliente->empresa_id;
                $endereco = EnderecoCliente::Create(objToArray($dados));
                return $endereco->id;
            }
        }

    }

    public static function setarEnderecoFrete($dados){
        $pedido = LojaPedido::find($dados->pedido_id);
        $pedido->endereco_id = $dados->endereco_id;
        $pedido->valor_frete = $dados->valor_frete;
        $pedido->status_id   = config("constantes.status.AGUARDANDO_PAGAMENTO");
        $resultado = $pedido->save();
        LojaPedido::somarTotal($dados->pedido_id);
        return $resultado;
    }

    public static function addItem($dados){
       // $empresa = Empresa::where("uuid", $dados->empresa_uuid)->first();

        $pedido = LojaPedido::find($dados->pedido_id);
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
                if($produto->usa_grade =="S"){
                    $grade = GradeProduto::where(["produto_id"=>$produto->id,"linha_id"=>$dados->linha_id,"coluna_id"=>$dados->coluna_id])->first();
                    $it->grade_produto_id = $grade->id ?? null;
                }

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
                $item = LojaItemPedido::Create(objToArray($it));
            }
        }
        return $item->id ?? null;
    }

    public static function logar($dados){
        $cliente = Cliente::where(["email" =>$dados->email,'password' => md5($dados->senha)] )->first();
        if ($cliente ) {
            if($dados->pedido_id){
                $pedido = LojaPedido::find($dados->pedido_id);
                $pedido->cliente_id = $cliente->id;
                $pedido->save();
            }
        }
        return $cliente;
    }
}

