<?php
namespace App\Services;


use App\Models\Emitente;
use App\Models\FinRecebimento;
use App\Models\ContaCorrente;
use App\Models\ClassificacaoFinanceira;

class RecebimentoService
{

    public static function inserirPeloPdvDuplicata($receber, $tipo_pagamento){
        $emitente = Emitente::where("empresa_id", $receber->empresa_id)->first();

        $recebimento = new \stdClass();
        $recebimento->empresa_id        = $receber->empresa_id ;
        $recebimento->usuario_id        = $receber->usuario_id;
        $recebimento->conta_receber_id  = $receber->id;
        $recebimento->descricao_recebimento = "Recebimento de Venda PDV " ;
        $recebimento->status_id         = config("constantes.status.PAGO");
        $recebimento->forma_pagto_id    = $tipo_pagamento;
        $recebimento->classificacao_financeira_id  = $emitente->pdv_classificacao_financeira_id ;
        $recebimento->conta_corrente_id = $emitente->pdv_conta_corrente_id ;
        $recebimento->data_recebimento  = hoje();
        $recebimento->valor_original    = $receber->valor;
        $recebimento->valor_recebido    = $receber->valor;
        $recebimento->juros             = $receber->total_juros;
        $recebimento->multa             = $receber->total_multa;
        $recebimento->desconto          = $receber->total_desconto;
        $recebimento->origem            = "PDV";

        return FinRecebimento::Create(objToArray($recebimento));
    }

    public static function inserirPeloDelivery($receber, $forma_pagto){
        $conta                          = ContaCorrente::where("empresa_id", $receber->empresa_id)->first();
        $classificacao                  = ClassificacaoFinanceira::where("empresa_id", $receber->empresa_id)->first();

        $recebimento = new \stdClass();
        $recebimento->empresa_id        = $receber->empresa_id ;
        $recebimento->usuario_id        = $receber->usuario_id;
        $recebimento->conta_receber_id  = $receber->id;
        $recebimento->descricao_recebimento = "Recebimento do Delivey" ;
        $recebimento->status_id         = config("constantes.status.PAGO");
        $recebimento->forma_pagto_id    = $forma_pagto;
        $recebimento->classificacao_financeira_id  = $classificacao->id;
        $recebimento->conta_corrente_id = $conta->id;

        $recebimento->data_recebimento  = hoje();
        $recebimento->valor_original    = $receber->valor;
        $recebimento->valor_recebido    = $receber->total_recebido;
        $recebimento->juros             = $receber->total_juros;
        $recebimento->multa             = $receber->total_multa;
        $recebimento->desconto          = $receber->total_desconto;
        $recebimento->origem            = "delivery";
        return FinRecebimento::Create(objToArray($recebimento));
    }

    public static function inserirPelaLojaPedido($receber, $forma_pagto){
        $conta                          = ContaCorrente::where("empresa_id", $receber->empresa_id)->first();
        $classificacao                  = ClassificacaoFinanceira::where("empresa_id", $receber->empresa_id)->first();

        $recebimento = new \stdClass();
        $recebimento->empresa_id        = $receber->empresa_id ;
        $recebimento->usuario_id        = $receber->usuario_id;
        $recebimento->conta_receber_id  = $receber->id;
        $recebimento->descricao_recebimento = "Recebimento da Loja Virtual" ;
        $recebimento->status_id         = config("constantes.status.PAGO");
        $recebimento->forma_pagto_id    = $forma_pagto;
        $recebimento->classificacao_financeira_id  = $classificacao->id;
        $recebimento->conta_corrente_id = $conta->id;

        $recebimento->data_recebimento  = hoje();
        $recebimento->valor_original    = $receber->valor;
        $recebimento->valor_recebido    = $receber->total_recebido;
        $recebimento->juros             = $receber->total_juros;
        $recebimento->multa             = $receber->total_multa;
        $recebimento->desconto          = $receber->total_desconto;
        $recebimento->origem            = "loja_virtual";
        return FinRecebimento::Create(objToArray($recebimento));
    }

    public static function inserirPelaCobranca($receber, $forma_pagto){
        $conta                          = ContaCorrente::where("empresa_id", $receber->empresa_id)->first();
        $classificacao                  = ClassificacaoFinanceira::where("empresa_id", $receber->empresa_id)->first();

        $recebimento                    = new \stdClass();
        $recebimento->empresa_id        = $receber->empresa_id ;
        $recebimento->usuario_id        = $receber->usuario_id;
        $recebimento->conta_receber_id  = $receber->id;
        $recebimento->descricao_recebimento = "Recebimento de Pagto de Cobrança " ;
        $recebimento->status_id         = config("constantes.status.PAGO");
        $recebimento->forma_pagto_id    = $forma_pagto;
        $recebimento->classificacao_financeira_id  = $classificacao->id;
        $recebimento->conta_corrente_id = $conta->id;

        $recebimento->data_recebimento  = hoje();
        $recebimento->valor_original    = $receber->valor;
        $recebimento->valor_recebido    = $receber->valor;
        $recebimento->juros             = $receber->total_juros;
        $recebimento->multa             = $receber->total_multa;
        $recebimento->desconto          = $receber->total_desconto;
        $recebimento->origem            = "cobranca";
       // $recebimento->conta_corrente_id = $emitente->conta_caixa_id ;
        $pago = FinRecebimento::Create(objToArray($recebimento));
    }

    public static function inserirPelaFatura($receber, $forma_pagto){
        $conta                          = ContaCorrente::where("empresa_id", $receber->empresa_id)->first();
        $classificacao                  = ClassificacaoFinanceira::where("empresa_id", $receber->empresa_id)->first();
        $recebimento                    = new \stdClass();
        $recebimento->empresa_id        = $receber->empresa_id ;
        $recebimento->usuario_id        = $receber->usuario_id;
        $recebimento->conta_receber_id  = $receber->id;
        $recebimento->descricao_recebimento = "Recebimento de Pagto de Cobrança " ;
        $recebimento->status_id         = config("constantes.status.PAGO");
        $recebimento->forma_pagto_id    = $forma_pagto;
        $recebimento->classificacao_financeira_id  = $classificacao->id;
        $recebimento->conta_corrente_id = $conta->id;

        $recebimento->data_recebimento  = hoje();
        $recebimento->valor_original    = $receber->valor;
        $recebimento->valor_recebido    = $receber->valor;
        $recebimento->juros             = $receber->total_juros;
        $recebimento->multa             = $receber->total_multa;
        $recebimento->desconto          = $receber->total_desconto;
        $recebimento->origem            = "cobranca";
       // $recebimento->conta_corrente_id = $emitente->conta_caixa_id ;
        $pago = FinRecebimento::Create(objToArray($recebimento));
    }

    public static function inserirPelaContaReceber($receber, $forma_pagto){
        $conta                          = ContaCorrente::where("empresa_id", $receber->empresa_id)->first();
        $classificacao                  = ClassificacaoFinanceira::where("empresa_id", $receber->empresa_id)->first();

        $recebimento                    = new \stdClass();
        $recebimento->empresa_id        = $receber->empresa_id ;
        $recebimento->usuario_id        = $receber->usuario_id;
        $recebimento->conta_receber_id  = $receber->id;
        $recebimento->descricao_recebimento = "Recebimento de Pagto de Cobrança " ;
        $recebimento->status_id         = config("constantes.status.PAGO");
        $recebimento->forma_pagto_id    = $forma_pagto;
        $recebimento->classificacao_financeira_id  = $classificacao->id;
        $recebimento->conta_corrente_id = $conta->id;

        $recebimento->data_recebimento  = hoje();
        $recebimento->valor_original    = $receber->valor;
        $recebimento->valor_recebido    = $receber->valor;
        $recebimento->juros             = $receber->total_juros;
        $recebimento->multa             = $receber->total_multa;
        $recebimento->desconto          = $receber->total_desconto;
        $recebimento->origem            = "cobranca";
       // $recebimento->conta_corrente_id = $emitente->conta_caixa_id ;
        $pago = FinRecebimento::Create(objToArray($recebimento));
    }
}

