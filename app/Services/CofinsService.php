<?php
namespace App\Services;

class CofinsService
{
    public static function calculo($item, $vBC, $tributacao, $produto){
        $pCOFINS = ($produto->pCOFINS) ? $produto->pCOFINS : $tributacao->pCOFINS; 
        $item->cstCOFINS	        = $tributacao->cstCOFINS		    ;  
        $item->vCOFINS              = null;       
        
        if(($item->cstCOFINS =='01') || ($item->cstCOFINS =='02')) {
            $item->pCOFINS      = $pCOFINS              ;
            $item->vBCCOFINS    = $vBC     ;           
            $item->vCOFINS      = $item->vBCCOFINS * ($item->pCOFINS/100);
        }else if($item->cstCOFINS =='03'){
            $item->vBCCOFINS        = null;
            $item->qBCProdConfis    = $item->qCom                       ;
            $item->vAliqProd_cofins = $tributacao->vAliqProd_cofins     ;
            $item->vCOFINS          = $item->vAliqProd_cofins * $item->qBCProdConfis;
        }else if($item->cstCOFINS =='99'){
            if($tributacao->pCOFINS){
                $item->vBCCOFINS  = $vBC     ;
                $item->pCOFINS    = $pCOFINS;
                $item->vCOFINS    = $vBC * ($item->pCOFINS/100);
            }else if($tributacao->vAliqProd_cofins){
                $item->vAliqProd_cofins = $tributacao->vAliqProd_cofins;
                $item->qBCProdConfis= $item->qCom ;
                $item->vBCCOFINS    = null;                
                $item->vCOFINS      = $item->vAliqProd_cofins * $item->qBCProdConfis;
            }else{
                $item->vBCCOFINS = null;
                $item->pCOFINS   = null;
                $item->vCOFINS   = null;
            }
        }else{
            $item->vBCCOFINS = null;
            $item->vCOFINS  = null;            
        }        
    }
    
    public static function recalculo($item, $vBC){        
        if(($item->cstCOFINS =='01') || ($item->cstCOFINS =='02')) {
            $item->vBCCOFINS    = $vBC     ;
            $item->vCOFINS      = $item->vBCCOFINS * ($item->pCOFINS/100);
        }else if($item->cstCOFINS =='03'){
            $item->vBCCOFINS        = null;
            $item->qBCProdConfis    = $item->qCom                       ;
            $item->vCOFINS          = $item->vAliqProd_cofins * $item->qBCProdConfis;
        }else if($item->cstCOFINS =='99'){
            if($item->pCOFINS){
                $item->vBCCOFINS  = $vBC     ;
                $item->vCOFINS    = $vBC * ($item->pCOFINS/100);
            }else if($item->vAliqProd_cofins){
                $item->vAliqProd_cofins = $item->vAliqProd_cofins;
                $item->qBCProdConfis= $item->qCom ;
                $item->vBCCOFINS    = null;
                $item->vCOFINS      = $item->vAliqProd_cofins * $item->qBCProdConfis;
            }else{
                $item->vBCCOFINS = null;
                $item->pCOFINS   = null;
                $item->vCOFINS   = null;
            }
        }else{
            $item->vBCCOFINS = null;
            $item->vCOFINS  = null;
        }
    }
   /* public static function calculoCofinsST($cofins, $item->vCOFINS){
        $item->vCOFINSST = 0;       
        
        if($tipo_calculost == '0'){
            $item->vCOFINSST = 0; 
        }else if($tipo_calculost == '1'){
            $valor_calculado = ($item->vBC * ($item->pCOFINSST /100) - $item->vCOFINS);
            if($valor_calculado<=0){
                $item->vCOFINSST = 0;
            }else{
                $item->vCOFINSST = $valor_calculado;
            }
        }else{
            $valor_calculado = (($item->vAliqProdst * $item->qCom) - $item->vCOFINS);
            if($valor_calculado<=0){
                $item->vCOFINSST = 0;
            }else{
                $item->vCOFINSST = $valor_calculado;
            }
        }
        
        
        return $item->vCOFINSST;
    }*/
}

