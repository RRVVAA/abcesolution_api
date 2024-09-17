<?php
namespace App\Services;


use App\Models\Nfce;
use App\Models\NfceItem;
use App\Models\Produto;

class ItemNotafiscalService{
    
    public static function inserir($i, $tributacao, $emitente ){        
        $produto        = Produto::find($i->produto_id);
        $nfce           = Nfce::find($i->nfce_id);
       
        $item           = new \stdClass();
        $item->orig     = $produto->origem ;
        $item->nfce_id   = $i->nfce_id ;
        $item->numero_item = $i->numero_item  ;
        $item->cProd    = $produto->id;
        $item->cEAN     = ($produto->gtin) ? $produto->gtin :"SEM GTIN";
        $item->xProd    = tiraAcento($produto->nome);
        $item->vProd    = $i->subtotal;
        $item->NCM      = tira_mascara($produto->ncm);
        $item->cBenef   = $produto->cbenef; //incluido no layout 4.00
        $item->EXTIPI   = $produto->tipi;        
        $item->CFOP     = tira_mascara($i->cfop);        
        $item->uCom     = tiraAcento($produto->unidade);
        $item->qCom     = $i->qtde;
        $item->vUnCom   = $i->valor;   
        $item->modBC    = $tributacao->modBC;
        $item->cEANTrib = ($produto->gtin) ? $produto->gtin :"SEM GTIN";
       
        if($produto->unidade_tributavel = null ||  $produto->unidade_tributavel== ''){
            $item->uTrib = tiraAcento($produto->unidade);
        }else{
            $item->uTrib = tiraAcento($produto->unidade_tributavel);
        }
        
        if($produto->quantidade_tributavel == 0){
            $item->qTrib = $i->qtde;
        }else{
            $item->qTrib = $produto->quantidade_tributavel * $i->qtde;
        }
        
        $item->vUnTrib  = $i->valor;
        $item->indTot   = 1; //ver depois        
        $item->vFrete   = $i->vFrete ;
        $item->vSeg     = $i->vSeg;
        $item->vDesc    = $i->vDesc ;
        $item->vOutro   = $i->vOutro;
        $item->infAdProd= $i->observacao ?? null;
        $item->xPed     = $i->nfce_id  ;
       // $item->nItemPed = $item->numero_item;
        $item->nFCI     = $i->nfci ?? null;          
        
        //IPI
        IpiService::calculo($item,  $tributacao)  ;        
       
        //PIS
        PisService::calculo($item, $tributacao);        
       
        // Confins
        CofinsService::calculo($item, $tributacao);
        
         //ICMS  
        $vBC = $item->vProd + $nfce->vFrete + $nfce->vSeg + $nfce->vOutro - $nfce->vDesc;  
        
       // $vBC = $item->vProd + $nfce->vFrete + $nfce->vSeg + $nfce->vOutro - $nfce->vDesc + $item->vIPI; 
        
        IcmsService::calculoICMS($item, $vBC, $tributacao);                  
             
        NfceItem::Create(objToArray($item));
        
        
    }
    
    
}

