<?php

namespace App\Observers;

use App\Models\GradeMovimento;
use App\Models\PdvItemVenda;
use App\Models\PdvVenda;
use App\Models\GradeProduto;

class ItemPdvVendaObserver
{
    public function created(PdvItemVenda $item)
    { 
        
        PdvVenda::somarTotal($item->venda_id );
        if($item->grade_produto_id){
            $grade = GradeProduto::find($item->grade_produto_id);
            $mov                    = new \stdClass();
            $mov->tipo_movimento_id = config("constantes.tipo_movimento.SEM_MOVIMENTO");
            $mov->empresa_id    	= $grade->empresa_id;            
            $mov->produto_id        = $item->produto_id;
            $mov->item_pdvvenda_id  = $item->id;
            $mov->pdvvenda_id       = $item->venda_id ;
            $mov->grade_id          = $item->grade_produto_id;
            $mov->ent_sai           = 'S';
            $mov->estorno           = 'N';
            $mov->data_movimento    = hoje();
            $mov->qtde_movimento    = $item->qtde;
            $mov->descricao         = "Saída Por Venda PDV- Item: #" . $item->id;
            if($mov->qtde_movimento > 0){
                GradeMovimento::Create(objToArray($mov));
            }
        }
        
    }
    
    public function deleted(PdvItemVenda $item)
    {
        PdvVenda::somarTotal($item->venda_id );
    }
}
