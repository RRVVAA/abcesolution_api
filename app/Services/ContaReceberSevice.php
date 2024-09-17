<?php
namespace App\Services;

use App\Models\Cliente;
use App\Models\FinContaReceber;

class ContaReceberSevice{

    public static function inserirPeloPdvDuplicata($duplicata){
        $receber                    = new \stdClass();
        $receber->cliente_id		= $duplicata->venda->cliente_id ? $duplicata->venda->cliente_id : $duplicata->venda->cliente_consumidor_id;
        $receber->empresa_id		= $duplicata->venda->empresa_id;
        $receber->usuario_id		= $duplicata->venda->usuario_id;
        $receber->pdvduplicata_id	= $duplicata->id;
        $receber->num_parcela		= 1;
        $receber->ult_parcela		= 1;
        $receber->data_emissao	    = hoje();
        $receber->data_vencimento	= $duplicata->dVenc;
        $receber->descricao	        = "Venda PDV #" . $duplicata->venda_id;
        $receber->valor	            = $duplicata->vDup;
        $receber->status_id         = config("constantes.status.ABERTO");
        $receber->total_juros       = 0;
        $receber->total_multa       = 0;
        $receber->total_desconto    = 0;
        $receber->total_liquido     = $receber->valor;
        $receber->total_recebido    = 0;
        $receber->origem            = "Venda PDV";
        $receber->total_restante    = $receber->valor;
        return FinContaReceber::Create(objToArray($receber));

    }

    public static function inserirPeloPedidoDoDelivery($pedido){
        $consumidor         = Cliente::where("eh_consumidor","S")->first();
        $receber = new \stdClass();
        $receber->cliente_id		= $consumidor->id;
        $receber->empresa_id		= $pedido->empresa_id;
        $receber->usuario_id		= 1;
        $receber->delivery_id	    = $pedido->id;
        $receber->num_parcela		= 1;
        $receber->ult_parcela		= 1;
        $receber->data_emissao	    = hoje();
        $receber->data_vencimento	= hoje();;
        $receber->descricao	        = "Venda Pelao Delivery #" . $pedido->id;
        $receber->valor	            = $pedido->total;
        $receber->status_id         = config("constantes.status.ABERTO");
        $receber->total_juros       = 0;
        $receber->total_multa       = 0;
        $receber->total_desconto    = 0;
        $receber->total_liquido     = $pedido->total;
        $receber->total_recebido    = $pedido->total;
        $receber->origem            = "delivery";
        $receber->total_restante    = 0;
		return  FinContaReceber::Create(objToArray($receber));
    }

    public static function inserirPeloPedidoDaLoja($pedido){

        $receber = new \stdClass();
        $receber->cliente_id		= $pedido->cliente_id;
        $receber->empresa_id		= $pedido->empresa_id;
        $receber->usuario_id		= 1;
        $receber->loja_pedido_id	= $pedido->id;
        $receber->num_parcela		= 1;
        $receber->ult_parcela		= 1;
        $receber->data_emissao	    = hoje();
        $receber->data_vencimento	= hoje();;
        $receber->descricao	        = "Venda Pela Loja Virtual #" . $pedido->id;
        $receber->valor	            = $pedido->valor_venda;
        $receber->status_id         = config("constantes.status.ABERTO");
        $receber->total_juros       = 0;
        $receber->total_multa       = 0;
        $receber->total_desconto    = $pedido->valor_desconto;
        $receber->total_liquido     = $pedido->valor_liquido;
        $receber->total_recebido    = $pedido->valor_liquido;
        $receber->origem            = "loja_virtual";
        $receber->total_restante    = 0;
		return  FinContaReceber::Create(objToArray($receber));

    }

    public static function inserirPelaFatura($pedido){

        $receber = new \stdClass();
        $receber->cliente_id		= $pedido->cliente_id;
        $receber->empresa_id		= $pedido->empresa_id;
        $receber->usuario_id		= 1;
        $receber->loja_pedido_id	= $pedido->id;
        $receber->num_parcela		= 1;
        $receber->ult_parcela		= 1;
        $receber->data_emissao	    = hoje();
        $receber->data_vencimento	= hoje();;
        $receber->descricao	        = "Venda Pela Loja Virtual #" . $pedido->id;
        $receber->valor	            = $pedido->valor_venda;
        $receber->status_id         = config("constantes.status.ABERTO");
        $receber->total_juros       = 0;
        $receber->total_multa       = 0;
        $receber->total_desconto    = $pedido->valor_desconto;
        $receber->total_liquido     = $pedido->valor_liquido;
        $receber->total_recebido    = $pedido->valor_liquido;
        $receber->origem            = "loja_virtual";
        $receber->total_restante    = 0;
		return  FinContaReceber::Create(objToArray($receber));

    }

}

