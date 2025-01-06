<?php

namespace App\Services;

use App\Models\CupomDesconto;
use App\Models\Emitente;
use App\Models\Impressao;
use App\Models\NaturezaOperacao;
use App\Models\Nfce;
use App\Models\PdvCaixa;
use App\Models\PdvDuplicata;
use App\Models\PdvItemVenda;
use App\Models\PdvVenda;
use App\Models\Produto;
use App\Models\Tributacao;
use App\Models\User;
use App\Repositorios\Contratos\PdvCaixaRepositorioInterface;
use App\Repositorios\Contratos\PdvVendaRepositorioInterface;
use App\Repositorios\Contratos\UsuarioRepositorioInterface;

class PdvVendaService
{

    protected $usuarioRepositorio;
    protected $pdvVendaRepositorio;
    protected $pdvCaixaRepositorio;


    public function __construct(PdvVendaRepositorioInterface $pdvVendaRepositorio, PdvCaixaRepositorioInterface $pdvCaixaRepositorio,
                                UsuarioRepositorioInterface  $usuarioRepositorio)
    {
        $this->usuarioRepositorio = $usuarioRepositorio;
        $this->pdvVendaRepositorio = $pdvVendaRepositorio;
        $this->pdvCaixaRepositorio = $pdvCaixaRepositorio;
    }

    public static function gerarVenda(string $uuid, $cupom_id = null)
    {
        $usuario = User::where("uuid", $uuid)->first();
        $emitente = Emitente::where("empresa_id", $usuario->empresa_id)->first();
        if ($usuario) {
            $caixa = PdvCaixa::where("usuario_abriu_id", $usuario->id)->where("status_id", config("constantes.status.ABERTO"))->first();
            if ($caixa) {
                $v = new \stdClass();
                $v->usuario_id = $usuario->id;
                $v->caixa_id = $caixa->id;
                $v->empresa_id = $usuario->empresa_id;
                $v->cliente_id = $emitente->cliente_consumidor;
                $v->cupom_desconto_id = $cupom_id;
                $v->valor_total = 0;
                $v->valor_desconto = 0;
                $v->desconto_per = 0;
                $v->acrescimo_valor = 0;
                $v->acrescimo_per = 0;
                $v->valor_acrescimo = 0;
                $v->valor_liquido = 0;
                $v->data_venda = hoje();
                $v->status_id = config("constantes.status.DIGITACAO");
                $venda = PdvVenda::where("caixa_id", $caixa->id)->where("status_id", config("constantes.status.DIGITACAO"))->first();
                if (!$venda) {
                    $venda = PdvVenda::Create(objToArray($v));
                }

                $retorno = new \stdClass();
                $retorno->id = $venda->id;
                $retorno->erro = "";
                return $retorno;
            } else {
                $retorno = new \stdClass();
                $retorno->id = null;
                $retorno->erro = "Não existe nenhum caixa aberto pelo usuario";
                return $retorno;
            }
        } else {
            $retorno = new \stdClass();
            $retorno->id = null;
            $retorno->erro = "Usuário não Permitido ou não localizacao";
            return $retorno;
        }
    }

    public static function aplicarCupom($dados)
    {
        $retorno = new \stdClass();
        $retorno->tem_erro = true;
        $retorno->cupom_desconto_id = null;
        $cupom = CupomDesconto::where("codigo", $dados->codigo_cupom)->first();
        $pedido = PdvVenda::find($dados->venda_id);

        if (!$pedido) {
            $retorno->erro = "Este Pedido não é válido ou não foi encontrado!";
            return $retorno;
        }

        if (!$cupom) {
            $retorno->erro = "Este Cupom não é válido ou não foi encontrado!";
            return $retorno;
        }

        if ($pedido->cupom_desconto_id) {
            $retorno->erro = "Não é mais possível usar um cupom para esta venda!";
            return $retorno;
        }

        foreach ($pedido->itens as $item) {
            $aplicar = true;
            $produto = $item->produto;
            $item->desconto_por_unidade = 0;
            $retorno->desconto_por_valor = $cupom->desconto_por_valor;
            $item->desconto_percentual = $cupom->desconto_percentual;
            $item->cupom_desconto_id = $cupom->id;

            $aplicar = CupomDescontoService::validarCupom($cupom, $produto);

            if ($aplicar == true) {
                if ($cupom->desconto_percentual > 0) {
                    $item->desconto_por_unidade = $cupom->desconto_percentual * $produto->valor_venda * 0.01;
                }

                if ($cupom->desconto_por_valor > 0) {
                    $item->desconto_por_unidade = $cupom->desconto_por_valor;
                }

                $item->subtotal_liquido = ($item->valor - $item->desconto_por_unidade) * $item->qtde;
                $item->total_desconto_item = $item->desconto_por_unidade * $item->qtde;

                $salvar = $item->save();
                if ($salvar) {
                    $pedido->cupom_desconto_id = $cupom->id;
                    $pedido->save();
                }
            }

        }

        return $pedido->id;
    }

    public static function excluirCupom($dados)
    {
        $retorno = new \stdClass();
        $retorno->tem_erro = true;
        $pedido = PdvVenda::find($dados->venda_id);

        foreach ($pedido->itens as $item) {
            $item->desconto_por_unidade = 0;
            $item->desconto_por_valor = 0;
            $item->desconto_percentual = 0;
            $item->cupom_desconto_id = null;
            $item->total_desconto_item = 0;
            $item->subtotal_liquido = $item->valor * $item->qtde;
            $item->save();
        }

        $pedido->cupom_desconto_id = null;
        $pedido->save();
        PdvVenda::somarTotal($pedido->id);
        return $pedido->id;
    }

    public function iniciarPdvVenda(string $uuid)
    {
        $usuario = $this->usuarioRepositorio->getUsuarioPorUuid($uuid);
        $emitente = Emitente::where("empresa_id", $usuario->empresa_id)->first();
        if ($usuario) {
            $caixa = PdvCaixa::where("usuario_abriu_id", $usuario->id)->where("status_id", config("constantes.status.ABERTO"))->first();
            if ($caixa) {
                $v = new \stdClass();
                $v->usuario_id = $usuario->id;
                $v->caixa_id = $caixa->id;
                $v->empresa_id = $usuario->empresa_id;
                $v->cliente_consumidor_id = $emitente->cliente_consumidor;
                $v->valor_total = 0;
                $v->valor_desconto = 0;
                $v->desconto_per = 0;
                $v->acrescimo_valor = 0;
                $v->acrescimo_per = 0;
                $v->valor_acrescimo = 0;
                $v->valor_liquido = 0;
                $v->data_venda = hoje();
                $v->status_id = config("constantes.status.PDVVENDA_INICIADA");
                $venda = PdvVenda::Create(objToArray($v));
                $retorno = new \stdClass();
                $retorno->id = $venda->id;
                $retorno->erro = "";
                return $retorno;
            } else {
                $retorno = new \stdClass();
                $retorno->id = null;
                $retorno->erro = "Não existe nenhum caixa aberto pelo usuario";
                return $retorno;
            }
        } else {
            $retorno = new \stdClass();
            $retorno->id = null;
            $retorno->erro = "Usuário não Permitido ou não localizacao";
            return $retorno;
        }
    }

    public function getVendaAbertaPorUsuario(string $uuid, $caixa_id)
    {
        $usuario = $this->usuarioRepositorio->getUsuarioPorUuid($uuid);
        $venda = $this->pdvVendaRepositorio->getVendaAbertaPorUsuario($usuario->id, $caixa_id);
        if ($venda) {
            return $venda;
        }
        return $this->pdvVendaRepositorio->novaVenda($usuario->id, $caixa_id, $usuario->empresa_id);
    }

    public static function getVendaPorId($venda_id)
    {
        return PdvVenda::where("id", $venda_id)->first();
    }

    public function listaPorCaixa($caixa_id)
    {
        return PdvVenda::where("caixa_id", $caixa_id)->get();
    }

    public function listaPorUsuario($usuario_uuid)
    {
        $usuario = $this->usuarioRepositorio->getUsuarioPorUuid($usuario_uuid);
        return $this->pdvVendaRepositorio->listaPorUsuario($usuario->id);
    }

    public function salvar($dados)
    {
        $venda = (object)$dados["venda"];
        $itens = $dados["itens"];
        $pagamentos = $dados["pagamentos"];

        $this->pdvVendaRepositorio->inserirItensVenda($venda->venda_id, $itens);
        $this->pdvVendaRepositorio->inserirPagamentos($venda->venda_id, $venda->caixa_id, $pagamentos);
        $this->pdvVendaRepositorio->salvar($venda);
        $this->pdvCaixaRepositorio->atualizar($venda->caixa_id);
        $venda_id = $venda->venda_id;

        //salvar NFCE
        $pdvvenda = PdvVenda::find($venda_id);
        $nfce = Nfce::where("venda_id", $venda_id)->first();
        $natureza_operacao = NaturezaOperacao::where("padrao", config('constantes.padrao_natureza.PDV'))->first();
        $tributacao = Tributacao::where(["natureza_operacao_id" => $natureza_operacao->id, "padrao" => "S"])->first();
        if (!$nfce) {
            PdvVenda::inserirNfcePelaVenda($pdvvenda, $natureza_operacao, $tributacao);
        }
        $nfce = Nfce::where("venda_id", $venda_id)->first();
        $this->transmitirNfcePelaVenda($venda_id);
        return $venda_id;
    }

    public function finalizarVenda($dados)
    {
        print_r($dados); exit;
        $venda_id = $dados["venda_id"];
        $pdvvenda = PdvVenda::find($venda_id);
        $num_pdv = $pdvvenda->caixa->num_pdv;

        //Lançar Estoque
        $tipo_movimento = config("constantes.tipo_movimento.SAIDA_VENDA_PDV");
        $descricao = "Saida PDV - Venda PDV: #" . $venda_id;
        MovimentoService::lancarEstoqueDoPdv($venda_id, $tipo_movimento, $descricao, $pdvvenda->empresa_id);
        $pdvvenda->status_id = config("constantes.status.CONCRETIZADO");
        $pdvvenda->cliente_cnpj = $dados["cliente_cnpj"];
        $pdvvenda->cliente_cpf = $dados["cliente_cpf"];

        $pdvvenda->save();

        PdvCaixa::atualizar($pdvvenda->caixa_id);
        $retorno = new \stdClass();
        $retorno->tem_erro = false;
        $retorno->venda_id = $venda_id;
        $retorno->protocolo = null;
        $retorno->tem_erro = false;
        $retorno->nfce_id = null;
        $retorno->protocolo = null;
        $retorno->chave = null;

        if ($num_pdv->transmitir_nfce == "S") {
            if ($dados["tem_pendencia"] == "N") {
                $resultado = self::transmitirNfcePelaVenda($venda_id);
                for ($i = 0; $i <= 3; $i++) {
                    if (!$resultado) {
                        sleep(3);
                    } else {
                        break;
                    }
                    $resultado = self::transmitirNfcePelaVenda($venda_id);
                }
                if (($resultado->tem_erro ?? null) == true) {
                    $retorno->tem_erro = true;
                    $retorno->erro = $resultado->erro;
                    return $retorno;
                }
                $retorno->nfce_id = $resultado->nfce_id ?? null;
                $retorno->protocolo = $resultado->protocolo ?? null;
                $retorno->chave = $resultado->chave ?? null;
            }
        }
        return $retorno;
    }

    public function transmitirNfcePelaVenda($pdvvenda_id)
    {
        $retorno = new \stdClass();
        $pdvvenda = PdvVenda::find($pdvvenda_id);
        $num_pdv = $pdvvenda->caixa->num_pdv;
        $nfce = Nfce::where("pdvvenda_id", $pdvvenda_id)->first();
        $natureza_operacao = NaturezaOperacao::where("padrao", config('constantes.padrao_natureza.PDV'))->first();
        $tributacao = Tributacao::where(["natureza_operacao_id" => $natureza_operacao->id, "padrao" => "S"])->first();
        $retorno->nfce_id = inserirNfcePelaVenda($pdvvenda, $natureza_operacao, $tributacao);


        if ($retorno->nfce_id) {
            $nfce = Nfce::where("pdvvenda_id", $pdvvenda_id)->first();
            if ($nfce) {
                $retorno->nfce_id = $nfce->id;
                $notafiscal = NotaFiscalService::prepararNfce($nfce);

                if ($nfce->status_id == config('constantes.status.EM_PROCESSAMENTO')) {
                    return $retorno;
                }

                if ($nfce->status_id == config('constantes.status.AUTORIZADO') || $nfce->status_id == config('constantes.status.CANCELADO') || $nfce->status_id == config('constantes.status.DENEGADO')) {
                    $retorno->protocolo = $nfce->protocolo;
                    $retorno->chave = $nfce->chave;
                    return $retorno;
                }

                $xml = NfceService::gerarNfce($notafiscal);

                if (!$xml->tem_erro) {
                    $retorno->chave = $xml->chave;
                    $xml_assinado = NfceService::assinarXml($xml->xml, $xml->chave, $notafiscal);
                    if (!$xml_assinado->tem_erro) {
                        $envio = NfceService::enviarXML($xml_assinado->xml, $xml->chave, $notafiscal);
                        if (!$envio->tem_erro) {
                            $retorno->protocolo = $envio->protocolo;
                            //Salvar para impressão
                            Impressao::Create(["chave" => $retorno->chave, "num_pdv_id" => $num_pdv->id]);
                        } else {
                            $retorno->tem_erro = true;
                            $retorno->erro = $envio->erro;
                            return $retorno;
                        }
                    } else {
                        $retorno->tem_erro = true;
                        $retorno->erro = $xml_assinado->erro;
                        return $retorno;
                    }
                } else {
                    $retorno->tem_erro = true;
                    $retorno->erro = $xml->erro;
                    return $retorno;
                }
            }
        }
    }


    public static function cancelarVenda($dados)
    {
        $venda = PdvVenda::find($dados->venda_id);
        if ($venda) {
            $duplicatas = PdvDuplicata::where("venda_id", $venda->id)->get();
            foreach ($duplicatas as $dup) {
                $dup->delete();
            }
            PdvItemVenda::where("venda_id", $dados->venda_id)->delete();
            $venda->delete();
        }
    }

    private function transmitirNfce($nfce)
    {
        $notafiscal = NotaFiscalService::prepararNfce($nfce);

        $xml = NfceService::gerarNfce($notafiscal);
        if (!$xml->tem_erro) {
            $xml_assinado = NfceService::assinarXml($xml->xml, $xml->chave, $notafiscal);
            if (!$xml_assinado->tem_erro) {
                $envio = NfceService::enviarXML($xml_assinado->xml, $xml->chave, $notafiscal);
                if (!$envio->tem_erro) {
                    return response()->json($envio, 200);
                } else {
                    return response()->json($envio, 201);
                }
            } else {
                return response()->json($xml_assinado, 201);
            }
        } else {
            return response()->json($xml, 201);
        }
    }

    public function gerarNfcePelaVenda($venda_id)
    {
        //salvar NFCE
        $pdvvenda = PdvVenda::find($venda_id);
        $nfce = Nfce::where("pdvvenda_id", $venda_id)->first();
        $natureza_operacao = NaturezaOperacao::where("padrao", config('constantes.padrao_natureza.PDV'))->first();
        $tributacao = Tributacao::where(["natureza_operacao_id" => $natureza_operacao->id, "padrao" => "S"])->first();
        if (!$nfce) {
            PdvVenda::inserirNfcePelaVenda($pdvvenda, $natureza_operacao, $tributacao);
        }
        $nfce = Nfce::where("pdvvenda_id", $venda_id)->first();
        return $nfce->id;
    }

    public function inserirItensVenda_excluir($dados)
    {
        $venda_id = $dados["venda_id"];
        $itens = $dados["itens"];
        //Inserir os itens
        $this->pdvVendaRepositorio->inserirItensVenda($venda_id, $itens);
        $cnpj = $dados["cliente_cnpj"];
        $cpf = $dados["cliente_cpf"];
        if ($cpf || $cnpj) {
            PdvVenda::where("id", $venda_id)->update(["cliente_cnpj" => $cnpj, "cliente_cpf" => $cpf]);
        }
        PdvVenda::somarTotal($venda_id);
        return $venda_id;
    }

    public function inserirItensVenda($dados)
    {
        $venda_id = $dados["venda_id"];
        $itens = $dados["itens"];
        //Inserir os itens
        $this->pdvVendaRepositorio->inserirItensVenda($venda_id, $itens);
        foreach ($itens as $item) {
            $this->inserirItem($item);
        }


        $cnpj = $dados["cliente_cnpj"];
        $cpf = $dados["cliente_cpf"];
        if ($cpf || $cnpj) {
            PdvVenda::where("id", $venda_id)->update(["cliente_cnpj" => $cnpj, "cliente_cpf" => $cpf]);
        }
        PdvVenda::somarTotal($venda_id);
        return $venda_id;
    }

    public function excluirItem($id, $idVenda)
    {
        PdvItemVenda::find($id)->delete();
        PdvVenda::somarTotal($idVenda);
    }

    public function inserirItem($dados)
    {
        $produto = null;
        if ($dados->barra_ou_id == "id") {
            $produto = Produto::where(["id" => $dados->q, "empresa_id" => $dados->empresa_id])->first();
        } else if ($dados->barra_ou_id == "barra") {
            $produto = Produto::where("codigo_barra", $dados->q)->where("empresa_id", $dados->empresa_id)->first();
        }

        if ($produto) {
            $item = new \stdClass();
            $item->venda_id = $dados->venda_id;
            $item->produto_id = $produto->id;
            $item->qtde = $dados->qtde;
            $item->valor = $produto->valor_venda;
            $item->subtotal = $item->valor * $item->qtde;
            $item->desconto_por_valor = $dados->desconto_por_valor;
            $item->desconto_percentual = $dados->desconto_percentual;
            $item->desconto_por_unidade = 0;

            if ($item->desconto_por_valor > 0) {
                $item->desconto_por_unidade = $item->desconto_por_valor;
            }
            if ($item->desconto_percentual > 0) {
                $item->desconto_por_unidade = $item->desconto_percentual * $item->valor * 0.01;
            }
            $item->subtotal_liquido = ($item->valor - $item->desconto_por_unidade) * $item->qtde;
            $item->total_desconto_item = $item->desconto_por_unidade * $item->qtde;

            $item = PdvItemVenda::Create(objToArray($item));
            return $item->venda_id;
        } else {
            return null;
        }
    }

    public function inserirPagamento($dados)
    {
        //Inserir pagamento
        $valor_total = $dados["vDup"];
        $qtde_parcela = $dados["qtde_vezes"];
        $parcela = number_format($valor_total / $qtde_parcela, 2, ".", "");
        $venda = PdvVenda::find($dados["venda_id"]);
        $soma_parcela = 0;
        for ($i = 0; $i < $qtde_parcela; $i++) {
            $pag = new \stdClass();
            $pag->venda_id = $dados["venda_id"];
            $pag->caixa_id = $dados["caixa_id"];
            $pag->tPag = $dados["tPag"];
            $pag->nDup = $i + 1;
            $pag->troco = $dados["troco"];
            $pag->dVenc = somarData(hoje(), $i * 30);

            if ($i == $qtde_parcela - 1) {
                $valor_parcela = $valor_total - $soma_parcela;
            } else {
                $valor_parcela = $parcela;
            }

            $soma_parcela += $parcela;
            $pag->vDup = $valor_parcela;

            $total = PdvDuplicata::where("venda_id", $pag->venda_id)->sum("vDup");
            $restante = $venda->valor_liquido - $total;
            if ($restante > 0) {
                PdvDuplicata::Create(objToArray($pag));
            }
        }

        return $dados["venda_id"];
    }

    public function enviarDescontoAcrescimento($dados)
    {
        $venda_id = $dados["venda_id"];
        $venda = PdvVenda::find($venda_id);
        $venda->acrescimo_per = $dados["acrescimo_percentual_total"];
        $venda->acrescimo_valor = $dados["acrescimo_por_valor_total"];
        $venda->desconto_per = $dados["desconto_percentual_total"];
        $venda->desconto_valor = $dados["desconto_por_valor_total"];

        //Inserir os itens
        $venda->save();
        PdvVenda::somarTotal($venda_id);
        return $venda_id;
    }

    public function excluirDuplicata($id)
    {

        /* FinContaReceber::where("pdvduplicata_id", $id)->delete();
         MovimentoConta::where("")->delete();*/
        PdvDuplicata::find($id)->delete();
    }


}

