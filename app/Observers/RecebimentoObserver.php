<?php

namespace App\Observers;

use App\Models\Cobranca;
use App\Models\FinContaReceber;
use App\Models\FinRecebimento;
use App\Models\Venda;
use App\Services\MovimentoContaBancariaService;

class RecebimentoObserver
{
    public function created(FinRecebimento $recebimento)
    { 
        $origem = null;
        $historico = null;
       
        //insere uma mavimentação
        if($recebimento->origem=="PDV"){
            $historico  = "Entrada via Venda PDV";
            $origem     = "Venda PDV";
        }
        if($recebimento->origem=="loja_virtual"){
            $historico  = "Entrada via Venda Loja Virtual";
            $origem     = "Venda loja virtual";
        }
        
        if($recebimento->origem=="cobranca"){
            $historico  = "Entrada via Recebimento de Fatura";
            $origem     = "Produto Recorrente";
        }
        
        MovimentoContaBancariaService::inserirMovimentoRecebimento($recebimento, $origem, $historico);        
        
        if($recebimento->conta_receber_id ){
            FinContaReceber::atualizar($recebimento->conta_receber_id);
            $conta_receber = $recebimento->conta_receber;
           
            if($conta_receber->venda_id){
                Venda::atualizarStatus($conta_receber->venda_id);
            }
            
            if($conta_receber->cobranca_id){                
                Cobranca::atualizarStatus($conta_receber->cobranca_id);
            }
        }
        
         
    }
    
   
}
