<?php
namespace App\Service;

use App\Models\NfeItem;

class ItemNotafiscalOperacaoService{
    
    public static function salvarItemDaNota($dados, $id){
       
        $item = new \stdClass();
      
        $item->numero_item			=  isset($dados->numero_item) ? $dados->numero_item : null		;
        $item->cProd                = isset($dados->cProd) ? $dados->cProd : null             ; 
        $item->CFOP                = isset($dados->CFOP) ? $dados->CFOP : null             ;  
        $item->cEAN                 = isset($dados->cEAN) ? $dados->cEAN : null              ;
        $item->xProd                = isset($dados->xProd) ? $dados->xProd : null                ;
        $item->NCM                  = isset($dados->NCM) ? $dados->NCM : null                   ;
        $item->CEST                 = isset($dados->CEST) ? $dados->CEST : null               ;
        $item->cBenef               = isset($dados->cBenef) ? $dados->cBenef : null              ;
        $item->NVE                  = isset($dados->NVE) ? $dados->NVE : null                 ;
        $item->EXTIPI               = isset($dados->EXTIPI) ? $dados->EXTIPI : null              ;    
        $item->uCom                 = isset($dados->uCom) ? $dados->uCom : null            ;
        $item->qCom                 = isset($dados->qCom) ?  getFloat($dados->qCom): null  ;       
        $item->vUnCom               = isset($dados->vUnCom) ?  getFloat($dados->vUnCom): null             ;    
        $item->vProd                = isset($dados->vProd) ?  getFloat($dados->vProd): null               ;    
        $item->cEANTrib             = isset($dados->cEANTrib) ? $dados->cEANTrib : null          ;    
        $item->uTrib                = isset($dados->uTrib) ? $dados->uTrib : null             ;   
        $item->qTrib                = isset($dados->qTrib) ?  getFloat($dados->qTrib): null              ;   
        $item->vUnTrib              = isset($dados->vUnTrib) ?  getFloat($dados->vUnTrib): null             ;
        $item->vFrete               = isset($dados->vFrete) ?  getFloat($dados->vFrete): null              ;
        $item->vSeg                 = isset($dados->vSeg) ?  getFloat($dados->vSeg): null                ;  
        $item->vDesc                = isset($dados->vDesc) ?  getFloat($dados->vDesc): null               ;
        $item->vOutro               = isset($dados->vOutro) ?  getFloat($dados->vOutro): null              ;   
        $item->indTot               = isset($dados->indTot) ? $dados->indTot : null            ;
        $item->xPed                 = isset($dados->xPed) ? $dados->xPed : null               ;
        $item->nItemPed             = isset($dados->nItemPed) ? $dados->nItemPed : null          ;
        $item->nFCI                 = isset($dados->nFCI) ? $dados->nFCI : null               ;   
        $item->cstIPI               = isset($dados->cstIPI) ? $dados->cstIPI : null             ;   
        $item->clEnq                = isset($dados->clEnq) ? $dados->clEnq : null              ;    
        $item->CNPJProd             = isset($dados->CNPJProd) ? $dados->CNPJProd : null           ;    
        $item->cSelo                = isset($dados->cSelo) ? $dados->cSelo : null              ;    
        $item->qSelo                = isset($dados->qSelo) ? $dados->qSelo : null             ;
        $item->cEnq                 = isset($dados->cEnq) ? $dados->cEnq : null               ;
        $item->vIPI                 = isset($dados->vIPI) ?  getFloat($dados->vIPI): null               ;
        $item->vBCIPI               = isset($dados->vBCIPI) ?  getFloat($dados->vBCIPI): null              ;
        $item->pIPI                 = isset($dados->pIPI) ?  getFloat($dados->pIPI): null               ;
        $item->qUnidIPI             = isset($dados->qUnidIPI) ?  getFloat($dados->qUnidIPI): null           ;
        $item->vUnidIPI             = isset($dados->vUnidIPI) ?  getFloat($dados->vUnidIPI): null            ;
        $item->tipo_calc_ipi        = isset($dados->tipo_calc_ipi) ? $dados->tipo_calc_ipi : null      ;
        $item->cstCOFINS            = isset($dados->cstCOFINS) ? $dados->cstCOFINS : null          ;
        $item->pCOFINS              = isset($dados->pCOFINS) ?  getFloat($dados->pCOFINS): null             ;
        $item->qBCProdConfis         = isset($dados->qBCProdConfis) ?  getFloat($dados->qBCProdConfis): null             ;
        $item->tipo_calc_cofins     = isset($dados->tipo_calc_cofins) ? $dados->tipo_calc_cofins : null   ;
        $item->vAliqProd_cofins     = isset($dados->vAliqProd_cofins) ?  getFloat($dados->vAliqProd_cofins): null    ;
        $item->vBCCOFINS            = isset($dados->vBCCOFINS) ?  getFloat($dados->vBCCOFINS): null          ;
        $item->vCOFINS              = isset($dados->vCOFINS) ?  getFloat($dados->vCOFINS): null             ;
        $item->tipo_calc_cofinsst   = isset($dados->tipo_calc_cofinsst) ? $dados->tipo_calc_cofinsst : null ;
        $item->pCOFINSST            = isset($dados->pCOFINSST) ?  getFloat($dados->pCOFINSST): null          ;
        $item->vAliqProd_cofinsst   = isset($dados->vAliqProd_cofinsst) ?  getFloat($dados->vAliqProd_cofinsst): null  ;
        $item->cstPIS               = isset($dados->cstPIS) ? $dados->cstPIS : null             ;
        $item->tipo_calc_pis        = isset($dados->tipo_calc_pis) ? $dados->tipo_calc_pis : null      ;
        $item->vBCPIS               = isset($dados->vBCPIS) ?  getFloat($dados->vBCPIS): null              ;
        $item->pPIS                 = isset($dados->pPIS) ?  getFloat($dados->pPIS): null                ;
        $item->vPIS                 = isset($dados->vPIS) ?  getFloat($dados->vPIS): null                ;
        $item->qBCProdPis           = isset($dados->qBCProdPis) ?  getFloat($dados->qBCProdPis): null                ;
        $item->vAliqProd_pis        = isset($dados->vAliqProd_pis) ?  getFloat($dados->vAliqProd_pis): null      ;
        $item->tipo_calc_pisst      =isset($dados->tipo_calc_pisst) ? $dados->tipo_calc_pisst : null    ;
        $item->pPISST               =isset($dados->pPISST) ?  getFloat($dados->pPISST): null              ;
        $item->vPISST               = isset($dados->vPISST) ?  getFloat($dados->vPISST): null             ;
        $item->vAliqProd_pisst      = isset($dados->vAliqProd_pisst) ?  getFloat($dados->vAliqProd_pisst): null     ;
        $item->orig                 = isset($dados->orig) ? $dados->orig : null               ;
        
        $item->cstICMS              = isset($dados->cstICMS) ?  getFloat($dados->cstICMS): null        ;
        $item->modBC                = isset($dados->modBC) ? $dados->modBC : null             ;
        $item->vBCICMS              = isset($dados->vBCICMS) ?  getFloat($dados->vBCICMS): null             ;
        $item->pICMS                = isset($dados->pICMS) ?  getFloat($dados->pICMS): null               ;
        $item->vICMS                = isset($dados->vICMS) ?  getFloat($dados->vICMS): null               ;
        $item->pFCP                 = isset($dados->pFCP) ?  getFloat($dados->pFCP): null                ;
        $item->vFCP                 = isset($dados->vFCP) ?  getFloat($dados->vFCP): null                ;
        $item->vBCFCP               = isset($dados->vBCFCP) ?  getFloat($dados->vBCFCP): null             ;
        $item->pMVAST               = isset($dados->pMVAST) ?  getFloat($dados->pMVAST): null              ;
        $item->pRedBCST             = isset($dados->pRedBCST) ?  getFloat($dados->pRedBCST): null           ;
        $item->vBCST                = isset($dados->vBCST) ?  getFloat($dados->vBCST): null              ;
        $item->pICMSST              = isset($dados->pICMSST) ?  getFloat($dados->pICMSST): null             ;
        $item->vICMSST              = isset($dados->vICMSST) ?  getFloat($dados->vICMSST): null             ;
        $item->vBCFCPST             = isset($dados->vBCFCPST) ?  getFloat($dados->vBCFCPST): null           ;
        $item->pFCPST               = isset($dados->pFCPST) ?  getFloat($dados->pFCPST): null              ;
        $item->vFCPST               = isset($dados->vFCPST) ?  getFloat($dados->vFCPST): null             ;
        $item->vICMSDeson           = isset($dados->vICMSDeson) ?  getFloat($dados->vICMSDeson): null          ;
        $item->motDesICMS           = isset($dados->motDesICMS) ? $dados->motDesICMS : null        ;
        $item->pRedBC               = isset($dados->pRedBC) ?  getFloat($dados->pRedBC): null             ;
        $item->vICMSOp              = isset($dados->vICMSOp) ?  getFloat($dados->vICMSOp): null            ;
        $item->pDif                 = isset($dados->pDif) ?  getFloat($dados->pDif): null                ;
        $item->vICMSDif             = isset($dados->vICMSDif) ?  getFloat($dados->vICMSDif): null            ;
        $item->vBCSTRet             = isset($dados->vBCSTRet) ?  getFloat($dados->vBCSTRet): null            ;
        $item->pST                  = isset($dados->pST) ?  getFloat($dados->pST): null                 ;
        $item->vICMSSTRet           = isset($dados->vICMSSTRet) ?  getFloat($dados->vICMSSTRet): null          ;
        $item->vBCFCPSTRet          = isset($dados->vBCFCPSTRet) ?  getFloat($dados->vBCFCPSTRet): null         ;
        $item->pFCPSTRet            = isset($dados->pFCPSTRet) ?  getFloat($dados->pFCPSTRet): null           ;
        $item->vFCPSTRet            = isset($dados->vFCPSTRet) ?  getFloat($dados->vFCPSTRet): null           ;
        $item->pRedBCEfet           = isset($dados->pRedBCEfet) ?  getFloat($dados->pRedBCEfet): null          ;
        $item->vBCEfet              = isset($dados->vBCEfet) ?  getFloat($dados->vBCEfet): null             ;
        $item->pICMSEfet            = isset($dados->pICMSEfet) ?  getFloat($dados->pICMSEfet): null          ;
        $item->vICMSEfet            = isset($dados->vICMSEfet) ?  getFloat($dados->vICMSEfet): null          ;
        $item->vICMSSubstituto      = isset($dados->vICMSSubstituto) ?  getFloat($dados->vICMSSubstituto): null     ;
        $item->modBCST              = isset($dados->modBCST) ?  getFloat($dados->modBCST): null             ;
        $item->pBCOp                = isset($dados->pBCOp) ?  getFloat($dados->pBCOp): null               ;
        $item->UFST                 = isset($dados->UFST) ? $dados->UFST : null               ;
        $item->vBCSTDest            = isset($dados->vBCSTDest) ?  getFloat($dados->vBCSTDest): null           ;
       
        $item->vICMSSTDest          = isset($dados->vICMSSTDest) ?  getFloat($dados->vICMSSTDest): null        ;
        $item->pCredSN              = isset($dados->pCredSN) ?  getFloat($dados->pCredSN): null            ;
        $item->vCredICMSSN          = isset($dados->vCredICMSSN) ?  getFloat($dados->vCredICMSSN): null        ;
        $item->vBCUFDest            = isset($dados->vBCUFDest) ?  getFloat($dados->vBCUFDest): null           ;
        $item->vBCFCPUFDest         = isset($dados->vBCFCPUFDest) ?  getFloat($dados->vBCFCPUFDest): null        ;
        $item->pFCPUFDest           = isset($dados->pFCPUFDest) ?  getFloat($dados->pFCPUFDest): null          ;
        $item->pICMSUFDest          = isset($dados->pICMSUFDest) ?  getFloat($dados->pICMSUFDest): null         ;
        $item->pICMSInter           = isset($dados->pICMSInter) ?  getFloat($dados->pICMSInter): null          ;
        $item->pICMSInterPart       = isset($dados->pICMSInterPart) ?  getFloat($dados->pICMSInterPart): null      ;
        $item->vFCPUFDest           = isset($dados->vFCPUFDest) ?  getFloat($dados->vFCPUFDest): null         ;
        $item->vICMSUFDest          = isset($dados->vICMSUFDest) ?  getFloat($dados->vICMSUFDest): null         ;       
        $item->vICMSUFRemet         = isset($dados->vICMSUFRemet) ?  getFloat($dados->vICMSUFRemet): null       ;   
        if($id){
            NfeItem::where("id", $id)->update(objToArray($item));
            $nfeItem = NfeItem::find($id);
        }else{
            $nfeItem = NfeItem::Create(objToArray($item));
        } 
            
        return $nfeItem;
    }
      
 }

