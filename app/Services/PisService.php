<?php
namespace App\Services;

class PisService
{
    public static function calculo($item, $vBC, $tributacao, $produto){
        $pPIS = ($produto->pPIS) ? $produto->pPIS : $tributacao->pPIS; 
        $vBC                = $item->vProd;
        $item->cstPIS       = $tributacao->cstPIS  			   ;    
                
        if(($item->cstPIS =='01') || ($item->cstPIS =='02')) {
            $item->vBCPIS   = $vBC      ;
            $item->pPIS     = $pPIS               ;
            $item->vPIS     = $item->vBCPIS * ($item->pPIS/100);
        }else if($item->cstPIS =='03'){
            $item->vBCPIS          = null;
            $item->qBCProdPis   = $item->qCom ;
            $item->vAliqProd_pis= $tributacao->vAliqProd_pis       ;
            $item->vPIS = $item->vAliqProd_pis  * $item->qBCProdPis ;
        }else if($item->cstPIS =='99'){
            if($tributacao->pPIS){
                $item->pPIS     = $pPIS;
                $item->vBCPIS   = $vBC      ;
                $item->vPIS     = $vBC  * ($item->pPIS/100);
            }else if($tributacao->vAliqProd_pis ){
                $item->vAliqProd_pis = $tributacao->vAliqProd_pis;
                $item->qBCProdPis   = $item->qCom ;
                $item->vBCPIS   = null;
                $item->vPIS     = $item->vAliqProd_pis  * $item->qBCProdPis ;
            }else {
                $item->vBCPIS   = null;
                $item->pPIS     = null;
                $item->vPIS     = null;
            }                
        }else{
            $item->vBCPIS = null;
            $item->vPIS = null;
        }               
    }
    
    public static function recalculo($item, $vBC){        
        if(($item->cstPIS =='01') || ($item->cstPIS =='02')) {
            $item->vBCPIS   = $vBC      ;
            $item->vPIS     = $item->vBCPIS * ($item->pPIS/100);
        }else if($item->cstPIS =='03'){
            $item->vBCPIS       = null;
            $item->qBCProdPis   = $item->qCom ;
            $item->vPIS         = $item->vAliqProd_pis  * $item->qBCProdPis ;
        }else if($item->cstPIS =='99'){
            if($item->pPIS){
                $item->vBCPIS   = $vBC      ;
                $item->vPIS     = $vBC  * ($item->pPIS/100);
            }else if($item->vAliqProd_pis ){
                $item->qBCProdPis   = $item->qCom ;
                $item->vBCPIS   = null;
                $item->vPIS     = $item->vAliqProd_pis  * $item->qBCProdPis ;
            }else {
                $item->vBCPIS   = null;
                $item->pPIS     = null;
                $item->vPIS     = null;
            }
        }else{
            $item->vBCPIS = null;
            $item->vPIS = null;
        }
    }
    
   /* public static function calculoPisST($item, $tributacao){
        $item->vPISST = 0;       
        
        if($item->tipo_calc_pis_st == '0'){
            $item->vPISST = 0; 
        }else if($item->tipo_calc_pis_st == '1'){
            $valor_calculado = ($item->vBCPIS * ($item->pPISST /100) - $item->vPIS);
            if($valor_calculado<=0){
                $item->vPISST = 0;
            }else{
                $item->vPISST = $valor_calculado;
            }
        }else{
            $valor_calculado = (($item->vAliqProd_pis * $item->qCom) - $item->vPIS);
            if($valor_calculado<=0){
                $item->vPISST = 0;
            }else{
                $item->vPISST = $valor_calculado;
            }
        }
        
        
        return $item->vPISST;
    }*/
}

