<?php

namespace App\Http\Controllers;

use App\Models\WebHook;
use App\Services\LojaPedidoService;
use Illuminate\Http\Request;
use MercadoPago\Payment;
use MercadoPago\SDK;
use Psy\Util\Json;
use App\Services\FaturaService;


class FaturaWebHookController extends Controller{
	public function __construct(){
        SDK::setAccessToken(env('MP_ACCESS_TOKEN'));
    }
	
    public function testePagamento($id_fatura){
        $pagamento = FaturaService::gerarPagamentoFatura($id_fatura);
        return response()->json($pagamento);
    }
    public function pix(Request $request){
        $dados = $request->all();		
        if(count($dados) == 0){
            return response()->json(["msg"=>"Nada Recebido", "status"=>"erro"], 422);
        }
        try{
			//SOMENTE PARA LOGS
            $w = new WebHook();
           // $w->identificador   = $dados["data"]["id"] ?? null;
            $w->dados_recebidos = Json::encode($request->all());
            $w->save();			
			//VERIFICAR SE FOI APROVADO
			 if(($dados["action"] ?? '') == 'payment.updated'){
				 $id = $dados["data"]["id"] ?? null;
				 if($id != null){
					$pagamento = Payment::find_by_id($id);
					if($pagamento->status == "approved"){
						$dadosPagto 					= new \stdClass();
						$dadosPagto->id 				= $pagamento->external_reference;
						$dadosPagto->transacao_id 		= $pagamento->id;
						$dadosPagto->forma_pagto_id		= config("constantes.forma_pagto.PIX");
						$dadosPagto->status_pagamento	= $pagamento->status;
						$dadosPagto->status_id          = config("constantes.status.PAGO");
						FaturaService::pagarFatura($dadosPagto);
					}						
				 }
			 }		
			
        }catch (\Exception $e){
			return response()->json(["msg"=>$e->getMessage()], 422);
        }
    }    
    
	public function obterPagamento($id){
		$pagamento = Payment::find_by_id($id);
		i($pagamento);
		//https://lojavirtual.erpmjailton.com.br/
	}
	
	public function cartao(Request $request){
        $dados = $request->all();
        if(count($dados) == 0){
            return response()->json(["msg"=>"Nada Recebido", "status"=>"erro"], 422);
        }
        try{
            $w = new WebHook();
            $w->dados = Json::encode($request->all());
            $w->save();
        }catch (\Exception $e){
           // \Log::error($e->getMessage());
        }
    }
	
	public function boleto(Request $request){
        $dados = $request->all();
        if(count($dados) == 0){
            return response()->json(["msg"=>"Nada Recebido", "status"=>"erro"], 422);
        }
        try{
            $w = new WebHook();
            $w->dados = Json::encode($request->all());
            $w->save();
        }catch (\Exception $e){
            //\Log::error($e->getMessage());
        }
    }
}
