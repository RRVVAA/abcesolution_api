<?php

namespace App\Http\Controllers;

use App\Models\LogMercadoPago;
use App\Models\MercadoPagoTransacao;
use App\Models\Parametro;
use App\Models\WebHook;
use Illuminate\Http\Request;
use MercadoPago\Payment;
use Psy\Util\Json;


class WebHookController extends Controller
{

    public function escuta(Request $request){
        $dados = $request->all();
		$w = new WebHook();
		$w->identificador   = $dados["data"]["id"] ?? null;
		$w->dados_recebidos = Json::encode($request->all());
		$w->save();


        if(($dados["action"] ?? '') == 'payment.updated'){
            $id = $dados["data"]["id"] ?? null;
            if($id != null){
                $logMp      = LogMercadoPago::where("transacao", $w->identificador)->first();

                if($logMp){
                    if($logMp->assinatura_id ){
                        $MP_ACCESS_TOKEN = env('MP_ACCESS_TOKEN');
                    }else{
                        $parametro  = Parametro::where("empresa_id", $logMp->empresa_id)->first();
                        $MP_ACCESS_TOKEN = $parametro->mercadopago_access_token;
                    }

                    \MercadoPago\SDK::setAccessToken($MP_ACCESS_TOKEN);

                    $payment = Payment::find_by_id($id);
                    $pagamento = new \stdClass();

                    if($payment->description){
                        if($payment->description=="loja_virtual" && $payment->external_reference){
                            $pagamento->loja_pedido_id = $payment->external_reference;
                        }

                        if($payment->description=="pdv" && $payment->external_reference){
                            $pagamento->pdv_venda_id = $payment->external_reference;
                        }

                        if($payment->description=="cobranca" && $payment->external_reference){
                            $pagamento->cobranca_id = $payment->external_reference;
                        }

                        if($payment->description=="fatura" && $payment->external_reference){
                            $pagamento->fatura_id = $payment->external_reference;
                        }

                        if($payment->description=="assinatura" && $payment->external_reference){
                            $pagamento->assinatura_id = $payment->external_reference;
                        }

                        if($payment->description=="delivery" && $payment->external_reference){
                            $pagamento->delivery_id = $payment->external_reference;
                        }
                    }

                    $pagamento->transacao_id        = $payment->id ?? null;
                    $pagamento->status              = $payment->status ?? null;
                    $pagamento->descricao           = $payment->description ?? null;
                    $pagamento->data_criacao        = $payment->date_created ?? null;
                    $pagamento->data_ultima_modificacao   = $payment->date_last_updated ?? null;
                    $pagamento->data_expiracao      = $payment->date_of_expiration ?? null;
                    $pagamento->data_aprovacao      = $payment->date_approved ?? null;
                    $pagamento->valor               = $payment->transaction_amount ?? null;
                    $pagamento->metodo_pagamento    = $payment->payment_method_id ?? null;
                    $pagamento->referencia_externa  = $payment->external_reference ?? null;

                    MercadoPagoTransacao::Create(objToArray($pagamento));
                }

            }

        }
        echo json_encode("ok");

    }


}
