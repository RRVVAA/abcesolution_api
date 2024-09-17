<?php
namespace App\Services;

use App\Models\FinFatura;
use App\Models\FinPagamento;
use App\Models\GestaoRecebimento;
use App\Models\Empresa;

class FaturaService{
    public static function pagarFatura($dados){
        try{            
            
            echo json_encode();
        } catch (\Exception $e) {
            echo json_encode($e->getMessage());
        }
    }
    
    public static function gerarPagamentoFatura($id_fatura){   
        $fatura  = FinFatura::find($id_fatura);
        $empresa = Empresa::find($fatura->empresa_id);
        
        //gerar pagamento para a Empresa
        $pag = new \stdClass();
        $pag->empresa_id                = $fatura->empresa_id;
        $pag->usuario_id                = 1;
        $pag->descricao_pagamento       = "Pagamento da Fatura #" .$fatura->id;
        $pag->forma_pagto_id            = 16 ;
        $pag->tipo_documento            = config("constantes.tipo_recorrencia.FATURA") ;
        $pag->documento_id              = $fatura->id;
        $pag->data_pagamento            = hoje();
        $pag->numero_documento          = $fatura->id;
        $pag->observacao                = "Pagamento de fatura";
        $pag->valor_original            = $fatura->valor;
        $pag->valor_pago                = $fatura->valor;
        $pagamento                      = FinPagamento::Create(objToArray($pag));        
        
        //Gerar Recebimento para o Gestor
        $gestor_receb                          = new \stdClass();
        $gestor_receb->empresa_id              = $fatura->empresa_id;
        $gestor_receb->usuario_id              = 1;
        $gestor_receb->descricao_recebimento   = "Recebimento da Fatura #" .$fatura->id;
        $gestor_receb->forma_pagto_id          = 16 ;
        $gestor_receb->tipo_documento          = config("constantes.tipo_recorrencia.FATURA") ;
        $gestor_receb->documento_id            = $fatura->id;
        $gestor_receb->data_recebimento        = hoje();
        $gestor_receb->numero_documento        = $fatura->id;
        $gestor_receb->observacao              = "Pagamento de fatura";
        $gestor_receb->valor_original          = $fatura->valor;
        $gestor_receb->juros                   = 0;
        $gestor_receb->desconto                = 0;
        $gestor_receb->multa                   = 0;
        $gestor_receb->valor_recebido          = $fatura->valor;
        $gestor_recebimento                    = GestaoRecebimento::Create(objToArray($gestor_receb));
        
        
        $fatura->pagamento_id                 = $pagamento->id;
        $fatura->recebimento_id               = $gestor_recebimento->id;
        $fatura->save(); 
        
        //Alterar o status da empresa         
        $empresa->data_vencimento             = somarData($fatura->data_vencimento,30 * $empresa->planopreco->recorrencia );
        $empresa->save();
        
        return $pagamento;
        
    }
}

