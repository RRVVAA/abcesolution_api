<?php
namespace App\Services;



class BaseCalculoService{
    public static function calculoVbcIcms($item, $tributacao){
        $vBC = $item->vProd;        
        if($item->vbc_somente_produto=="S"){
            $vBC = $item->vUnCom;
        } 
        
        if($tributacao->vbc_desconto=="S"){
            //$vBC -= $item->vDesc ;
            $vBC -= $item->desconto_rateio ;
        }
        
        if($tributacao->vbc_ipi=="S"){
            $vBC += $item->vIPI ;
        }
        
        if($tributacao->vbc_outros=="S"){
            $vBC += $item->vOutro ;
        }
        
        if($tributacao->vbc_seguro=="S"){
            $vBC += $item->vSeg ;
        }
        
        if($tributacao->vbc_frete=="S"){
            $vBC += $item->vFrete ;
        }        
        
        //echo $item->vProd ." - " . $item->vDesc." + " . $item->vIPI . " + ". $item->vOutro."+" . $item->vSeg."+" . $item->vFrete;
       // exit;
        return $vBC;
    }
    
    public static function calculoVbcIpi($item, $tributacao){
        $vBC = $item->vProd;
        if($item->vbc_somente_produto=="S"){
            $vBC = $item->vUnCom;
        }
        
        if($tributacao->ipi_desconto=="S"){
            $vBC -= $item->desconto_rateio ;
        }
        
        
        if($tributacao->ipi_outros=="S"){
            $vBC += $item->vOutro ;
        }
        
        if($tributacao->ipi_seguro=="S"){
            $vBC += $item->vSeg ;
        }
        
        if($tributacao->ipi_frete=="S"){
            $vBC += $item->vFrete ;
        }
        
        return $vBC;
    }
    
    public static function calculoVbcPis($item, $tributacao){
        $vBC = $item->vProd;
        if($item->vbc_somente_produto=="S"){
            $vBC = $item->vUnCom;
        }
        
        if($tributacao->pis_desconto=="S"){
            $vBC -= $item->desconto_rateio ;
        }
        
        if($tributacao->pis_ipi=="S"){
            $vBC += $item->vIPI ;
        }
        
        if($tributacao->pis_outros=="S"){
            $vBC += $item->vOutro ;
        }
        
        if($tributacao->pis_seguro=="S"){
            $vBC += $item->vSeg ;
        }
        
        if($tributacao->pis_frete=="S"){
            $vBC += $item->vFrete ;
        }
        
        return $vBC;
    }
    
    public static function calculoVbcCofins($item, $tributacao){
        $vBC = $item->vProd;
        if($item->vbc_somente_produto=="S"){
            $vBC = $item->vUnCom;
        }
        
        if($tributacao->cofins_desconto=="S"){
            $vBC -= $item->desconto_rateio ;
        }
        
        if($tributacao->cofins_ipi=="S"){
            $vBC += $item->vIPI ;
        }
        
        if($tributacao->cofins_outros=="S"){
            $vBC += $item->vOutro ;
        }
        
        if($tributacao->cofins_seguro=="S"){
            $vBC += $item->vSeg ;
        }
        
        if($tributacao->cofins_frete=="S"){
            $vBC += $item->vFrete ;
        }
        
        return $vBC;
    }
    
    //Recalculo
    public static function reCalculaVbcIcms($item){
        $vBC = $item->vProd;
        if($item->vbc_somente_produto=="S"){
            $vBC = $item->vUnCom;
        }
        
        if($item->vbc_desconto=="S"){
            $vBC -= $item->desconto_rateio ;
        }
        
        if($item->vbc_ipi=="S"){
            $vBC += $item->vIPI ;
        }
        
        if($item->vbc_outros=="S"){
            $vBC += $item->vOutro ;
        }
        
        if($item->vbc_seguro=="S"){
            $vBC += $item->vSeg ;
        }
        
        if($item->vbc_frete=="S"){
            $vBC += $item->vFrete ;
        }
        
        return $vBC;
    }
    
    public static function reCalculaVbcIpi($item){
        $vBC = $item->vProd;
        if($item->vbc_somente_produto=="S"){
            $vBC = $item->vUnCom;
        }
        
        if($item->ipi_desconto=="S"){
            $vBC -= $item->desconto_rateio ;
        }      
                
        if($item->ipi_outros=="S"){
            $vBC += $item->vOutro ;
        }
        
        if($item->ipi_seguro=="S"){
            $vBC += $item->vSeg ;
        }
        
        if($item->ipi_frete=="S"){
            $vBC += $item->vFrete ;
        }
        
        return $vBC;
    }
    
    public static function reCalculaVbcPis($item){
        $vBC = $item->vProd;
        if($item->vbc_somente_produto=="S"){
            $vBC = $item->vUnCom;
        }
        
        if($item->pis_desconto=="S"){
            $vBC -= $item->desconto_rateio ;
        }
        
        if($item->pis_ipi=="S"){
            $vBC += $item->vIPI ;
        }
        
        if($item->pis_outros=="S"){
            $vBC += $item->vOutro ;
        }
        
        if($item->pis_seguro=="S"){
            $vBC += $item->vSeg ;
        }
        
        if($item->pis_frete=="S"){
            $vBC += $item->vFrete ;
        }
        
        return $vBC;
    }
    
    public static function reCalculaVbcCofins($item){
        $vBC = $item->vProd;
        if($item->vbc_somente_produto=="S"){
            $vBC = $item->vUnCom;
        }
        
        if($item->cofins_desconto=="S"){
            $vBC -= $item->desconto_rateio ;
        }
        
        if($item->cofins_ipi=="S"){
            $vBC += $item->vIPI ;
        }
        
        if($item->cofins_outros=="S"){
            $vBC += $item->vOutro ;
        }
        
        if($item->cofins_seguro=="S"){
            $vBC += $item->vSeg ;
        }
        
        if($item->cofins_frete=="S"){
            $vBC += $item->vFrete ;
        }
        
        return $vBC;
    }  
    
}

