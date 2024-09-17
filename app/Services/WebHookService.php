<?php
namespace App\Services;

use App\Models\Assinatura;
use App\Models\Cobranca;
use App\Models\ComandaPedido;
use App\Models\Emitente;
use App\Models\FinContaPagar;
use App\Models\FinFatura;
use App\Models\LojaPedido;
use App\Models\PdvDuplicata;
use App\Models\PdvVenda;
use App\Models\FinContaReceber;

class  WebHookService
{

    public static function confirmarPagamentoAssinatura($dados){
		$assinatura                        = Assinatura::find($dados->id);
        $assinatura->status_id             = config("constantes.status.ATIVO");
        $assinatura->eh_teste              = "N";
        $assinatura->dias_bloqueia         = 0;
        $assinatura->bloqueado_pelo_gestor = "N";
        $assinatura->liberado_pelo_gestor  = "N";
        $assinatura->data_aquisicao        = hoje();
        $assinatura->save();
        $assinatura                        = Assinatura::find($dados->id);

        $emitente                           = Emitente::where("empresa_id", $assinatura->empresa_id)->first();

        //gerar parcelas
        $dados->classificacao_financeira_id= $emitente->assinatura_classificacao_financeira_id;
        $dados->conta_corrente_id          = $emitente->assinatura_conta_corrente_id;
        $dados->pagar_fatura               = "S";
        FaturaService::gerarParcelas($assinatura, $dados);
    }

    public static function confirmarPagamentoFatura($dados){
        $fatura         = FinFatura::find($dados->id);
        //gerar parcelas

        $emitente                           = Emitente::where("empresa_id", $fatura->empresa_id)->first();
        $dados->classificacao_financeira_id= $emitente->assinatura_classificacao_financeira_id;
        $dados->conta_corrente_id          = $emitente->assinatura_conta_corrente_id;
        $dados->pagar_fatura               = "S";
        $retorno = FinanceiroService::inserirRecebimentoDeFatura($fatura->id, $dados);

        //atualiza Conta Pagar
        $contaPagar = FinContaPagar::where("fatura_id", $fatura->id)->first();
        $contaPagar->total_restante = 0;
        $contaPagar->status_id      = config("constantes.status.PAGO");
        $contaPagar->save();

        //Ultima fatura paga
        $assinatura = Assinatura::find($fatura->assinatura_id);
        $assinatura->ultima_fatura_paga= $fatura->id;
        $assinatura->save();

    }

    //Confirma o pagamento vindo do webhook
    public static function confirmarPagamentoDelivery($dados){
        $deliveryPedido 	= ComandaPedido::find($dados->id);
        $contaReceber   = ContaReceberSevice::inserirPeloPedidoDoDelivery($deliveryPedido);
        if($contaReceber){
            RecebimentoService::inserirPeloDelivery($contaReceber, $dados->forma_pagto);
        }

        $deliveryPedido->data_pagamento         = hoje();
        $deliveryPedido->transacao_id           = $dados->transacao_id;
        $deliveryPedido->status_financeiro_id   = config("constantes.status.PAGO");
        $deliveryPedido->status_id              = config("constantes.status.NOVO");
        $deliveryPedido->save();
    }







































    //Confirma o pagamento vindo do webhook
    public static function confirmarPagamentoPdv($dados){

        $pdvVenda           = PdvVenda::find($dados->id);

        $pag                = new \stdClass();
        $pag->venda_id      = $dados->id;
        $pag->caixa_id      = $pdvVenda->caixa_id;
        $pag->tPag          = $dados->forma_pagto;
        $pag->nDup          = 1;
        $pag->dVenc         = hoje();
        $pag->vDup          = $dados->valor;
        $pag->transacao_id  = $dados->transacao_id;
        PdvDuplicata::Create(objToArray($pag));

    }

    public static function confirmarPagamentoPedidoLoja($dados){
		$lojaPedido 	= LojaPedido::find($dados->id);
        $contaReceber   = ContaReceberSevice::inserirPeloPedidoDaLoja($lojaPedido);
        if($contaReceber){
            RecebimentoService::inserirPelaLojaPedido($contaReceber, $dados->forma_pagto);
        }

        $tipo_movimento         = config("constantes.tipo_movimento.SAIDA_VENDA_LOJA_VIRTUAL");
        $descricao              = "Saida Loja Virutal - Pedido: #" . $lojaPedido->id;
        MovimentoService::lancarEstoqueDoPedidoDaLoja($lojaPedido->id, $tipo_movimento, $descricao, $lojaPedido->empresa_id);
        $lojaPedido->data_pagamento         = hoje();
        $lojaPedido->transacao_id           = $dados->transacao_id;
        $lojaPedido->status_financeiro_id   = config("constantes.status.PAGO");
        $lojaPedido->status_id              = config("constantes.status.FINALIZADO");
        $lojaPedido->save();
    }



    public static function confirmarPagamentoContaReceber($dados){
        $contaReceber = FinContaReceber::find($dados->id);
        if($contaReceber){
            RecebimentoService::inserirPelaContaReceber($contaReceber,$dados->forma_pagto);
        }

       /* $tipo_movimento         = config("constantes.tipo_movimento.SAIDA_VENDA_LOJA_VIRTUAL");
        $descricao              = "Saida Loja Virutal - Pedido: #" . $fatura->id;
        MovimentoService::lancarEstoqueDoPedidoDaLoja($fatura->id, $tipo_movimento, $descricao, $fatura->empresa_id);

        $fatura->status_id  = config("constantes.status.FINALIZADO");;
        $fatura->save();*/
    }

    public static function confirmarPagamentoCobranca($dados){
        $cobranca       = Cobranca::find($dados->id);
        if($cobranca){
            $contaReceber   = FinContaReceber::where("cobranca_id", $cobranca->id)->first();

            if($contaReceber){
                RecebimentoService::inserirPelaCobranca($contaReceber, $dados->forma_pagto);
            }

            $cobranca->status_financeiro_id = config("constantes.status.PAGO");
            $cobranca->status_id            = config("constantes.status.FINALIZADO");
            $cobranca->data_pagamento       = hoje();
            $cobranca->save();
        }

    }



}

