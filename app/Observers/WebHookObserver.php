<?php

namespace App\Observers;

use App\Models\Assinatura;
use App\Models\FinFatura;
use App\Models\MercadoPagoTransacao;
use App\Services\WebHookService;

class WebHookObserver
{
    public function created(MercadoPagoTransacao $transacao)
    {

        if($transacao->status=="approved"){
            $forma_pagto = null;

            if($transacao->metodo_pagamento == "pix"){
                $forma_pagto = config("constantes.forma_pagto.PIX");
            }

            if($transacao->metodo_pagamento == "cartao_credito"){
                $forma_pagto = config("constantes.forma_pagto.CARTAO_CREDITO");
            }

            if($transacao->metodo_pagamento == "cartao_debito"){
                $forma_pagto = config("constantes.forma_pagto.CARTAO_DEBITO");
            }

            if($transacao->metodo_pagamento == "boleto"){
                $forma_pagto = config("constantes.forma_pagto.BOLETO_BANCARIO");
            }

            if(!$forma_pagto ){
                $forma_pagto = config("constantes.forma_pagto.DEPOSITO_BANCARIO");
            }

            $pedido = new \stdClass();
            $pedido->forma_pagto        = $forma_pagto;
            $pedido->valor              = $transacao->valor;
            $pedido->transacao_id       = $transacao->transacao_id;

            if($transacao->delivery_id){
                $pedido->id =$transacao->delivery_id;
                WebHookService::confirmarPagamentoDelivery($pedido);

             }

            if($transacao->assinatura_id){
                $pedido->id     = $transacao->assinatura_id;
                $assinatura     = Assinatura::find($transacao->assinatura_id);
                if(!$assinatura->ultima_fatura_paga){
                    WebHookService::confirmarPagamentoAssinatura($pedido);
                }
            }
            if($transacao->fatura_id){
                $pedido->id =$transacao->fatura_id;
                $fatura = FinFatura::find($transacao->fatura_id);
                if($fatura->status_id == config("constantes.status.ABERTO")){
                    WebHookService::confirmarPagamentoFatura($pedido);
                }
            }











            if($transacao->cobranca_id){
                $pedido->id =$transacao->cobranca_id;
                WebHookService::confirmarPagamentoCobranca($pedido);
            }



            if($transacao->loja_pedido_id){
               $pedido->id =$transacao->loja_pedido_id;
               WebHookService::confirmarPagamentoPedidoLoja($pedido);

            }

            if($transacao->pdv_venda_id){
                $pedido->id = $transacao->pdv_venda_id;
                WebHookService::confirmarPagamentoPdv($pedido);
            }




        }

    }


}
