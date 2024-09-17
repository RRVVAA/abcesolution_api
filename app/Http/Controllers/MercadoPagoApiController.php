<?php

namespace App\Http\Controllers;

use App\Http\Resources\PdvVendaResource;
use App\Services\MercadoPagoService;
use App\Services\PdvVendaService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MercadoPagoApiController extends Controller{
    protected $pdvVendaService;

    public function __construct(PdvVendaService $pdvVendaService){
        $this->pdvVendaService = $pdvVendaService;
    }


    public function buscarTransacaoMercadoPago($id){
        $pagamento = MercadoPagoService::buscarTransacaoMercadoPago($id);
        return response()->json(["data"=>$pagamento]);
    }

    public function transferencia($id_pedido){
        $pedido = new \stdClass();

        $pedido->id                 = $id_pedido;
        $pedido->transacao_id       = "121212121";
        $pedido->status_pagamento   = "approved";
        $pedido->forma_pagto_id     = "16";
        $pedido->status_detalhe     = "1";
        $pedido->hash               = Str::random(20);
        $pedido->status_id          = config("constantes.status.PAGO");

        //PedidoService::pagarPorTransferencia($pedido);

       // PagamentoService::gerarVenda($id_pedido);
        return redirect()->route('pagar.finalizado', $id_pedido);
    }

    public function pix(Request $request){
        $dados = (object) $request->all();
        return MercadoPagoService::pix($dados);
    }

    public function cartao(Request $request){
        $dados = (object) $request->all();
        return MercadoPagoService::cartao($dados);
    }

    public function boleto(Request $request){
        $dados = (object) $request->all();
        return MercadoPagoService::boleto($dados);
    }

    public function verificaSeCobrancaPagaNoPix($cobranca_id){
        $achou =  MercadoPagoService::verificaSeCobrancaPagaNoPix($cobranca_id);
        return response()->json(["data"=>$achou]);
    }

	 public function verificaSePedidoPagoNoPix($pedido_id){
        $achou =  MercadoPagoService::verificaSePedidoPagoNoPix($pedido_id);
        return response()->json(["data"=>$achou]);
    }

	public function verificaSeAssinaturaPagaNoPix($fatura_id){
        $achou =  MercadoPagoService::verificaSeAssinaturaPagaNoPix($fatura_id);
        return response()->json(["data"=>$achou]);
    }

    public function verificaSeFaturaPagaNoPix($fatura_id){
        $achou =  MercadoPagoService::verificaSeFaturaPagaNoPix($fatura_id);
        return response()->json(["data"=>$achou]);
    }

    public function verificaPagamentoPix($id_venda){
        $achou =  MercadoPagoService::verificaPagamentoPix($id_venda);
        if($achou > -1){
            $retorno = $this->pdvVendaService->getVendaPorId($id_venda);
            return new PdvVendaResource($retorno);
        }else{
            return response()->json(["data"=>-1]);
        }
    }

    public function verificaSeDeliveryPagaNoPix($delivery_id){
        $achou =  MercadoPagoService::verificaSeDeliveryPagaNoPix($delivery_id);
        return response()->json(["data"=>$achou]);
    }




}
