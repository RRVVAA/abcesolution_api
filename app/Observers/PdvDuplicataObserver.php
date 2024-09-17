<?php

namespace App\Observers;

use App\Models\FinContaReceber;
use App\Models\FinRecebimento;
use App\Models\MovimentoConta;
use App\Models\PdvDuplicata;
use App\Services\ContaReceberSevice;
use App\Services\RecebimentoService;


class PdvDuplicataObserver
{    
    public function created(PdvDuplicata $duplicata){ 
        
        $receber= ContaReceberSevice::inserirPeloPdvDuplicata($duplicata);
       // if($duplicata->tPag  == config("constantes.forma_pagto.DINHEIRO") || $duplicata->transacao_id !=null ){
            RecebimentoService::inserirPeloPdvDuplicata($receber, $duplicata->tPag);
       // }
       
            if($duplicata->tPag==config("constantes.forma_pagto.CREDITO_LOJA")){
                $cliente = $duplicata->venda->cliente;
                $cliente->credito_utilizado += $duplicata->vDup;
                $cliente->credito_disponivel -= $duplicata->vDup;
                $cliente->save();
            }
     }
    
     public function Deleting(PdvDuplicata $duplicata){
         $receber = FinContaReceber::where("pdvduplicata_id", $duplicata->id);
         foreach($receber->get() as $r){
              $recebimentos = FinRecebimento::where("conta_receber_id", $r->id);
              foreach ($recebimentos->get() as $re){
                  MovimentoConta::where("recebimento_id", $re->id)->delete();
              }
              $recebimentos->delete();
         }
         $receber->delete(); 
     } 
     
     public  function deleted(PdvDuplicata $duplicata){
         if($duplicata->tPag==config("constantes.forma_pagto.CREDITO_LOJA")){
             $cliente = $duplicata->venda->cliente;
             $cliente->credito_utilizado -= $duplicata->vDup;
             $cliente->credito_disponivel += $duplicata->vDup;
             $cliente->save();
         }
         
     }
     
   
}
