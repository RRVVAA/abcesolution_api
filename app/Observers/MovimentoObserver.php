<?php

namespace App\Observers;

use App\Models\Movimento;
use App\Services\EstoqueService;

class MovimentoObserver
{
    public function created(Movimento $mov)
    {   
        
        if($mov->ent_sai=="E"){
            EstoqueService::adicionarEstoque($mov->produto_id, $mov->qtde_movimento);
        }else if($mov->ent_sai=="S"){
            EstoqueService::subtrairEstoque($mov->produto_id, $mov->qtde_movimento);
        }        
        
    }
    
    public function deleted(Movimento $mov)
    {
        if($mov->ent_sai=="E"){
            EstoqueService::subtrairEstoque($mov->produto_id, $mov->qtde_movimento);
        }else if($mov->ent_sai=="S"){
            EstoqueService::adicionarEstoque($mov->produto_id, $mov->qtde_movimento);
        } 
    }
}
