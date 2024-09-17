<?php
namespace App\Services;

use App\Models\Emitente;
use App\Models\TributacaoIva;

class IcmsService
{
    public static function calculoIcms($item, $vBC, $tributacao, $pIcms, $destinatario, $produto, $aliquota=null, $iva=null){ 
        //$item->cstICMS  = $tributacao->cstICMS;       
        
        if($item->cstICMS=="00"){                
            $item->modBC    = $tributacao->modBC;
            $item->vBCICMS  = $vBC  ;            
            $item->pICMS    = $pIcms; 
            $item->vICMS    = $item->vBCICMS * ($item->pICMS / 100);
            self::calculoFcp($item, $vBC, $tributacao);
        }else if($item->cstICMS=="10"){            
            //Tributação Normal
            $item->vBCICMS  = $vBC  ;
            $item->modBC    = $tributacao->modBC;
            $item->pICMS    = $pIcms;
            $item->vICMS    = $item->vBCICMS * ($item->pICMS / 100); 
            
            
            
            self::calculoSt($item, $vBC, $tributacao, $produto, $aliquota, $iva);  
            self::calculoFcp($item, $vBC, $tributacao);
            self::calculoFcpSt($item, $vBC);
        }else if($item->cstICMS=="20"){
            $item->modBC    = $tributacao->modBC;
            $item->vBCICMS  = $vBC  ;
            $item->pICMS    = $pIcms;
            
            $pRedBC   = ($produto->pRedBC) ? $produto->pRedBC : 0 ;
            if($pRedBC==0 && $tributacao->pRedBC){
                $pRedBC = $tributacao->pRedBC;
            }            
            $item->pRedBC   = $pRedBC;
            $base_reduzida  = $item->vBCICMS - ($item->pRedBC/100 * $item->vBCICMS);
            $item->vBCICMS  = $base_reduzida ;
            $item->vICMS    = $base_reduzida * ($item->pICMS / 100);
            
            $item->vICMSDeson   = $vBC * ($pIcms / 100);
            $item->motDesICMS   = $tributacao->motDesICMS;
            
            self::calculoFcp($item, $vBC, $tributacao);
            
         }else if($item->cstICMS=="30"){
            $item->vBCICMS  = $vBC  ;
            $item->modBC    = $tributacao->modBC;
            $item->pICMS    = $pIcms;
            $item->vICMS    = $item->vBCICMS * ($item->pICMS / 100); 
            $item->destaca_icms = 'N';
            self::calculoSt($item, $vBC, $tributacao, $produto, $aliquota, $iva); 
            
            $item->vICMSDeson   = $vBC * ($pIcms / 100);
            $item->motDesICMS = $tributacao->motDesICMS; 
            
        }else if($item->cstICMS=="40" || $item->cstICMS=="41" || $item->cstICMS=="50" ){ 
            $item->destaca_icms = 'N';
            $item->pICMS        = $pIcms;
            $item->vICMSDeson   = $vBC * ($pIcms / 100);
            $item->motDesICMS   = $tributacao->motDesICMS; 
            
        }else if($item->cstICMS=="51"){            
            $item->modBC    = $tributacao->modBC;
            $item->vBCICMS  = $vBC  ;
            $item->pICMS    = $pIcms;
            $item->pDif     = ($produto->pDif) ? $produto->pDif : 0;
            if( $item->pDif==0 && $tributacao->pDif){
                $item->pDif = $tributacao->pDif;
            }            
            $item->vICMSOp   = $item->vBCICMS * ($item->pICMS / 100);
            $item->vICMSDif  = $item->vICMSOp * ($item->pDif / 100);
            $item->vICMS     = $item->vICMSOp  - $item->vICMSDif;                
            
        }else if($item->cstICMS=="60" ){
            $item->destaca_icms = 'N';
            $item->pICMS        = $pIcms;
            $item->vBCSTRet     = 0;
            $item->vICMSTRet    = 0;
            $item->vICMSDeson   = $vBC * ($pIcms / 100);
            $item->motDesICMS   = $tributacao->motDesICMS;
        }else if($item->cstICMS=="70"){
            $item->modBC    = $tributacao->modBC;
            $item->vBCICMS  = $vBC  ;
            $item->pICMS    = $pIcms;
            
            $item->vICMSDeson   = $vBC * ($pIcms / 100);
            $item->motDesICMS   = $tributacao->motDesICMS;
            
            $pRedBC   = ($produto->pRedBC) ? $produto->pRedBC : 0 ;      
            if($pRedBC==0 && $tributacao->pRedBC){
                $pRedBC = $tributacao->pRedBC;
            }
            
            $item->pRedBC   = $pRedBC;
            $base_reduzida  = $item->vBCICMS - ($item->pRedBC/100 * $item->vBCICMS);
            $item->vBCICMS  = $base_reduzida ;
            $item->vICMS    = $base_reduzida * ($item->pICMS / 100);           
            
            self::calculoSt($item, $vBC, $tributacao, $produto, $aliquota, $iva);  
            
        } elseif($item->cstICMS=="90"){
            $item->vBCICMS  = $vBC  ;
            $item->modBC    = $tributacao->modBC;
            $item->modBCST  = $tributacao->modBCST;
            $item->pRedBC   = 0;
            $item->pICMS    = 0;
            $item->vICMS    = 0;
            $item->vBCST    = 0;
            $item->pICMSST  = 0;
            $item->vICMSST  = 0;   
        
        }elseif($item->cstICMS=="101"){ 
            $item->destaca_icms = 'N';
            $emitente           = Emitente::where("empresa_id", $tributacao->empresa_id)->first();              
            $item->pCredSN      = ($emitente->pCredSN) ? $emitente->pCredSN: 0;
            $item->vCredICMSSN  = $item->vProd * ($item->pCredSN / 100);
           
            
        }else if($item->cstICMS=="201"){
            $emitente       = Emitente::where("empresa_id", $tributacao->empresa_id)->first();
            $item->pCredSN  = ($emitente->pCredSN) ? $emitente->pCredSN: 0;
            $item->destaca_icms = 'N';
            $item->vBCICMS  = $vBC  ;
            $item->modBC    = $tributacao->modBC;
            $item->pICMS    = $pIcms;
            $item->vICMS    = $item->vBCICMS * ($item->pICMS / 100);
            $item->vCredICMSSN  = $item->vProd * ($item->pCredSN / 100);
            self::calculoSt($item, $vBC, $tributacao, $produto, $aliquota, $iva);
            
        }else if($item->cstICMS=="202" || $item->cstICMS=="203" ){
            $item->vBCICMS  = $vBC  ;
            $item->destaca_icms = 'N';
            $item->modBC    = $tributacao->modBC;
            $item->pICMS    = $pIcms;
            $item->vICMS    = $item->vBCICMS * ($item->pICMS / 100);
			
            self::calculoSt($item, $vBC, $tributacao, $produto, $aliquota, $iva);
			
        }else if($item->cstICMS=="500"  ){
            $item->destaca_icms = 'N';
            $item->pICMS        = null;
            $item->vICMS        = null;
            $item->vBCSTRet     = 0;
            $item->vICMSTRet    = 0;
			/*
				$item->vICMSDeson   = $vBC * ($pIcms / 100);
				$item->motDesICMS   = $tributacao->motDesICMS;
			*/
			
			
			
        }else if($item->cstICMS=="900"){  
            $item->modBC    = $tributacao->modBC;
            $item->modBCST  = $tributacao->modBCST;
            
            $item->vBCICMS  = $vBC ;
            $item->vBCFCPST = $vBC;
            $item->pICMS    = $pIcms;
            
            //Redução da Base de Cálculo           
            if($item->cst900_redbc =='S'){
                $pRedBC   = ($produto->pRedBC) ? $produto->pRedBC : 0 ;
                if($pRedBC==0 && $tributacao->pRedBC){
                    $pRedBC = $tributacao->pRedBC;
                }                
                $item->pRedBC   = $pRedBC;
                $base_reduzida  = $item->vBCICMS - ($item->pRedBC/100 * $item->vBCICMS);
                $item->vBCICMS  = $base_reduzida ;
            }
            if($item->cst900_icms =='S'){
                $item->vICMS    = $item->vBCICMS * ($item->pICMS / 100);
            }
                        
            //Substituição tributária
            if($item->cst900_st =='S'){
                self::calculoSt($item, $vBC, $tributacao, $produto, $aliquota, $iva);
            }
            
            if($item->cst900_credisn =='S'){
                $emitente           = Emitente::where("empresa_id", $tributacao->empresa_id)->first();
                $item->pCredSN      = ($emitente->pCredSN) ? $emitente->pCredSN: 0;
                $item->vCredICMSSN  = $item->vProd * ($item->pCredSN / 100);
            }
                       
        }        
        
    }
    
    public static function calculoFcp($item, $vBC, $tributacao){
        if($tributacao->pFCP){
            $item->vBCFCP   = $vBC;
            $item->pFCP     = $tributacao->pFCP;
            $item->vFCP     = $vBC * ($item->pFCP / 100);
        }
    }
    
    public static function calculoFcpSt(){
        
    }
    
    public static function calculaDifal($item, $vbc){
        $tributacao = TributacaoIva::first();
        
        $item->vBCUFDest     = $vbc ;//Valor da BC do ICMS na UF de destino
        $item->pFCPUFDest    = $item->pFCP; //Percentual do ICMS relativo ao Fundo de Combate à Pobreza (FCP) na UF de destino
        $item->pICMSUFDest   = $tributacao->aliqInterestadualIcms; //Alíquota interna da UF de destino
        $item->pICMSInter    = $tributacao->aliqInternoIcms;//Alíquota interestadual das UF envolvidas
        $item->pICMSInterPart= 100; //Percentual provisório de partilha do ICMS Interestadual
        
        
        $valorOrigem        = $item.vProd  * ($item.pICMSInter / 100); //ICMS da operação interestadual para UF destino
        $valorDestino       = $vbc * ( $item.pICMSUFDest / 100);  //ICMS da operação interna na UF destino
        $valorDiferencial   = $valorDestino - $valorOrigem;
        
        $item->vFCPUFDest   =  $vbc * ($item->pFCPUFDest  / 100);//Valor do ICMS relativo ao Fundo de Combate à Pobreza (FCP) da UF de destino
        $item->vICMSUFDest  = ($valorDiferencial) * ($item->pICMSInterPart/ 100); //Valor do ICMS Interestadual para a UF de destino
        $item->vICMSUFRemet = 0;
      
    }
    public static function calculoSt($item, $vBC, $tributacao, $produto, $icms_estado, $iva){
        $aliquota_intra = $icms_estado->aliquota_origem; 
        $aliquota_inter = $iva->aliquota_destino;
        $pRedBCST       = ($produto->pRedBCST) ? $produto->pRedBCST : 0 ;
        $mva            = ($produto->pMVAST) ? $produto->pMVAST : 0 ;
        $item->modBCST  = 4;
        if($iva){
            if($iva->pIcmsIntra){
                $aliquota_intra = $iva->pIcmsIntra;
            }
            
            if($iva->pIcmsInter){
                $aliquota_inter = $iva->pIcmsInter;
            }
            
            if($iva->modBCST){
                $item->modBCST = $iva->modBCST;
            }
            
            //Se o procuto não tiver redução e o o iva tiber
            if($pRedBCST==0 && $iva->pRedBCST){
                $pRedBCST = $iva->pRedBCST;
            }            
           
            if($mva==0 && $iva->pMVAST){
                $mva = $iva->pMVAST;
            }            
        }
     
        $item->pMVAST       = $mva;
        $item->pICMSIntra   = $aliquota_intra;
        $item->pICMSInter   = $aliquota_inter;
        
        $item->pICMSST      = $aliquota_intra;

        //Base do ICMS ST
        $item->vBCST   = $vBC * (1 + ($item->pMVAST/100));        
        if($pRedBCST){
            $base_reduzida  = $item->vBCST - ($pRedBCST/100 * $item->vBCST) ;
            if($item->cstICMS=="70"){
                $item->pRedBCST = $pRedBCST;
                $item->vBCST    = $base_reduzida;
            }
        }        
        
       
        $icmsst        = $item->vBCST * $aliquota_intra * 0.01;
        $vICMSST       = $icmsst - $item->vICMS ;
        
        $item->vICMSST =($vICMSST<=0) ? 0 : $vICMSST;
    }
 
    
    public static function recalculoIcms($item, $vBC){
        $item->vBCICMS = $vBC;
        if($item->cstICMS=="00"){
            $item->vICMS    = $item->vBCICMS * ($item->pICMS / 100);
        }else if($item->cstICMS=="10"){            
            $item->vICMS    = $item->vBCICMS * ($item->pICMS / 100);
            self::reCalculoSt($item, $vBC);            
            
        }else if($item->cstICMS=="20"){
            $item->vBCICMS  = $vBC;
            $base_reduzida  = $item->vBCICMS - ($item->pRedBC/100 * $item->vBCICMS);
            $item->vBCICMS  = $base_reduzida;
            $item->vICMS    = $base_reduzida * ($item->pICMS / 100);
            
            $item->vICMSDeson   = $vBC * ($item->pICMS / 100);
            
        }else if($item->cstICMS=="30"){
            $item->vBCICMS = $vBC;
            $item->destaca_icms = 'N';
            $item->vICMS    = $item->vBCICMS * ($item->pICMS / 100);
            $item->vICMSDeson   = $vBC * ($item->pICMS / 100);
            self::reCalculoSt($item, $vBC); 
            
        }else if($item->cstICMS=="40" || $item->cstICMS=="41" || $item->cstICMS=="50" ){
            $item->destaca_icms = 'N';
            $item->vICMSDeson   = $vBC * ($item->pICMS / 100);
            
        }else if($item->cstICMS=="51"){
            $item->vBCICMS  = $vBC  ;
            $item->vICMSOp   = $item->vBCICMS * ($item->pICMS / 100);
            $item->vICMSDif  = $item->vICMSOp * ($item->pDif / 100);
            $item->vICMS     = $item->vICMSOp  - $item->vICMSDif;
        }else if($item->cstICMS=="60" ){
            $item->vICMSDeson   = $vBC * ($item->pICMS / 100);
        }else if($item->cstICMS=="70"){
            $item->vBCICMS  = $vBC  ;
            $base_reduzida  = $item->vBCICMS - ($item->pRedBC/100 * $item->vBCICMS);
            $item->vBCICMS  = $base_reduzida;
            $item->vICMS    = $base_reduzida * ($item->pICMS / 100);
            $item->vICMSDeson   = $vBC * ($item->pICMS / 100);
            self::reCalculoSt($item, $vBC);
            
        } elseif($item->cstICMS=="90"){
            //
            
        }elseif($item->cstICMS=="101"){
            $item->vCredICMSSN  = $item->vProd * ($item->pCredSN / 100);
            
        }else if($item->cstICMS=="201"){
            $item->destaca_icms = 'N';
            $item->vBCICMS  = $vBC  ;
            $item->vICMS    = $item->vBCICMS * ($item->pICMS / 100);
            $item->vCredICMSSN  = $item->vProd * ($item->pCredSN / 100);
            self::reCalculoSt($item, $vBC);
            
        }else if($item->cstICMS=="202" || $item->cstICMS=="203" ){
            $item->destaca_icms = 'N';
            $item->vBCICMS  = $vBC  ;
            $item->vICMS    = $item->vBCICMS * ($item->pICMS / 100);
            self::reCalculoSt($item, $vBC);
        }else if($item->cstICMS=="900"){            
            //Redução da Base de Cálculo
            if($item->cst900_redbc =='S'){
                $base_reduzida  = $item->vBCICMS - ($item->pRedBC/100 * $item->vBCICMS);
                $item->vBCICMS  = $base_reduzida ;
            }
            
            if($item->cst900_icms =='S'){
                $item->vICMS    = $item->vBCICMS * ($item->pICMS / 100);
            }
            
            //Substituição tributária
            if($item->cst900_st =='S'){
                self::reCalculoSt($item, $vBC);
            }
            
            if($item->cst900_credisn =='S'){
                $item->vCredICMSSN  = $item->vProd * ($item->pCredSN / 100);
            }
            
        }        
        
    }
    
    public static function reCalculoSt($item, $vBC){
        //Base do ICMS ST
        $item->vBCST   = $vBC * (1 + ($item->pMVAST/100));
        if($item->pRedBCST){
            $base_reduzida  = $item->vBCST - ($item->pRedBCST/100 * $item->vBCST) ;
            if($item->cstICMS=="70"){
                $item->vBCST    = $base_reduzida;
            }
        }        
        $icmsst        = $item->vBCST * $item->pICMSIntra * 0.01;
        $vICMSST       = $icmsst - $item->vICMS ;        
        $item->vICMSST =($vICMSST<=0) ? 0 : $vICMSST;
    }
    
}

