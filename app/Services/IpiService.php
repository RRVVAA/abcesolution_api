<?php
namespace  App\Services;

class IpiService{
    
    public static function calculo($item, $vBC, $tributacao, $produto){
        $pIPI = ($produto->pIPI) ? $produto->pIPI : $tributacao->pIPI; 
        //i($produto);
        $item->cstIPI 		= $tributacao->cstIPI 	;        
        $item->CNPJProd 	= $tributacao->CNPJProd ;
        $item->cSelo 		= $tributacao->cSelo 	;
        $item->qSelo 		= $tributacao->qSelo 	;
        $item->cEnq 		= $tributacao->cEnq 	;     
              
               
        if($item->tipo_calc_ipi==1){
            
            $item->pIPI     = $pIPI 	;
            $item->vBCIPI   = $vBC                        ;
            $item->vIPI     = $item->vBCIPI * ($item->pIPI/100)     ;
        }else if($item->tipo_calc_ipi==2){
            $item->qUnidIPI 	= $item->qCom 	          ;
            $item->vUnidIPI		= $tributacao->vUnidIPI 	      ; 
            $item->vIPI          = $item->vUnidIPI * $item->qUnidIPI ;            
        }         
    }
    
    public static function recalculo($item, $vBC){        
        if($item->tipo_calc_ipi==1){
            $item->vBCIPI   = $vBC                        ;
            $item->vIPI     = $item->vBCIPI * ($item->pIPI/100)     ;
        }else if($item->tipo_calc_ipi==2){
            $item->vIPI         = $item->vUnidIPI * $item->qUnidIPI ;
        }
    }
}

