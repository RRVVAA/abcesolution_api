<?php

namespace App\Observers;

use App\Models\Nfce;
use App\Models\NfceItem;
use App\Models\Nfe;
use App\Models\NfeItem;
use App\Models\Produto;
use App\Models\Tributacao;


class ItemNfceObserver
{
    public function creating(NfceItem $item){  
        $produto        = Produto::find($item->cProd);
        $nfce           = Nfce::find($item->nfce_id);
        $tributacao     = Tributacao::getTributacaoPadrao($nfce->natureza_operacao_id, $produto->id, $nfce->empresa_id);

        $item->orig     = $produto->origem ;
        $item->cEAN     = ($produto->gtin) ? $produto->gtin :"SEM GTIN";
        $item->xProd    = tiraAcento($produto->nome);
        $item->NCM      = tira_mascara($produto->ncm);
        $item->cBenef   = $produto->cbenef; //incluido no layout 4.00
        $item->EXTIPI   = $produto->tipi;
        $item->CEST     = $produto->cest;
        
        
        $item->uCom     = tiraAcento($produto->unidade);
        $item->cEANTrib = ($produto->gtin) ? $produto->gtin :"SEM GTIN";
        $item->xPed     = $item->nfce_id  ;
        $item->cstIPI   = $tributacao->cstIPI;
        $item->cstPIS   = $tributacao->cstPIS  			   ;
        $item->cstCOFINS= $tributacao->cstCOFINS		    ;
        $item->cstICMS  = $tributacao->cstICMS;
        $item->CFOP     = $tributacao->cfop ;
        $item->tipo_calc_ipi      =  $tributacao->tipo_calc_ipi;
        $item->vbc_somente_produto=  $tributacao->vbc_somente_produto;
        $item->vbc_frete          =  $tributacao->vbc_frete;
        $item->vbc_ipi            =  $tributacao->vbc_ipi;
        $item->vbc_outros         =  $tributacao->vbc_outros;
        $item->vbc_seguro         =  $tributacao->vbc_seguro;
        $item->vbc_desconto       =  $tributacao->vbc_desconto;
        
        $item->ipi_somente_produto=  $tributacao->ipi_somente_produto;
        $item->ipi_frete          =  $tributacao->ipi_frete;
        $item->ipi_outros         =  $tributacao->ipi_outros;
        $item->ipi_seguro         =  $tributacao->ipi_seguro;
        
        $item->pis_somente_produto=  $tributacao->pis_somente_produto;
        $item->pis_frete          =  $tributacao->pis_frete;
        $item->pis_ipi            =  $tributacao->pis_ipi;
        $item->pis_outros         =  $tributacao->pis_outros;
        $item->pis_seguro         =  $tributacao->pis_seguro;
        $item->pis_desconto       =  $tributacao->pis_desconto;
        
        $item->cofins_somente_produto=  $tributacao->cofins_somente_produto;
        $item->cofins_frete       =  $tributacao->cofins_frete;
        $item->cofins_ipi         =  $tributacao->cofins_ipi;
        $item->cofins_outros      =  $tributacao->cofins_outros;
        $item->cofins_seguro      =  $tributacao->cofins_seguro;
        $item->cofins_desconto    =  $tributacao->cofins_desconto;
        
        $item->cst900_icms        =  $tributacao->cst900_icms;
        $item->cst900_redbc       =  $tributacao->cst900_redbc;
        $item->cst900_credisn     =  $tributacao->cst900_credisn;
        $item->cst900_st          =  $tributacao->cst900_st;
        $item->cst900_redbcst     =  $tributacao->cst900_redbcst;
        
        
        if($produto->unidade_tributavel = null ||  $produto->unidade_tributavel== ''){
            $item->uTrib = tiraAcento($produto->unidade);
        }else{
            $item->uTrib = tiraAcento($produto->unidade_tributavel);
        }
        
        if($produto->quantidade_tributavel == 0){
            $item->qTrib = $item->qCom;
        }else{
            $item->qTrib = $produto->quantidade_tributavel * $item->qCom;
        }
        
        $item->vUnTrib  = $item->vUnCom;
        $item->indTot   = 1; //ver depois
    }
        
    
    public function created(NfceItem $item){
       // echo "item - created<br>";
        //Atualizando o campo vProd da Nota
        
        $total_itens    = NfceItem::where("nfce_id",$item->nfce_id)->sum("vProd");
        $total_liquido  = NfceItem::where("nfce_id",$item->nfce_id)->sum("subtotal_liquido");
        $nfce           = Nfce::find($item->nfce_id);       
        $nfce->vProd    = $total_itens;
        $nfce->vProd_liquido = $total_liquido;
        $nfce->save();        
        refazTodosCalculos($nfce);
        atualizarTotaisImpostosDaNota($nfce->id);
     }
      
   
    public function deleted(NfeItem $item){ 
        $nfe = Nfe::find($item->nfe_id);      
        refazTodosCalculos($nfe);
        atualizarTotaisImpostosDaNota($nfe->id );
    }
    
   
}
