<?php

use App\Models\Emitente;
use App\Models\IcmsEstado;
use App\Models\NaturezaOperacao;
use App\Models\Nfce;
use App\Models\NfceItem;
use App\Models\Produto;
use App\Models\Tributacao;
use App\Models\TributacaoIva;
use App\Models\TributacaoProduto;
use App\Services\BaseCalculoService;
use App\Services\CofinsService;
use App\Services\IcmsService;
use App\Services\IpiService;
use App\Services\PisService;

function refazTodosCalculos($nfce){
    $itens              = NfceItem::where("nfce_id", $nfce->id)->get();
    $qtdeItem           = count($itens);
    $natureza_operacao  = NaturezaOperacao::where("id", $nfce->natureza_operacao_id)->first();
    $tributacao_geral   = Tributacao::where(["natureza_operacao_id" =>$natureza_operacao->id,"padrao"=>"S"])->first();

    $emitente = Emitente::where('empresa_id', $nfce->empresa_id)->first();
    $tributacaoPadrao = Tributacao::find($emitente->tributacao_padrao);
    
    $j                  = 1;
    $total_desc_item    = 0;
    if($qtdeItem > 0){
        foreach($itens as $item){
            $item->proporcao = $item->vProd/$nfce->vProd * 100;
            
            $desconto_rateio = 0;
            if($nfce->vDesc){
                $desconto_rateio = ($item->proporcao/100)* $nfce->vDesc;
                $desconto_rateio = number_format($desconto_rateio,2);
                if($qtdeItem==$j){
                    $desconto_rateio = $nfce->vDesc - $total_desc_item;
                }
                $total_desc_item += $desconto_rateio;
            }                        
            
            $rateio_outro = 0;
            if($nfce->vOutro){
                $rateio_outro  = ($item->proporcao/100)* $nfce->vOutro;
            }
            
            $item->vOutro           = $rateio_outro;
            $item->desconto_rateio  = $desconto_rateio;
            $item->vDesc            = $item->desconto_rateio ;
            
            
            $produto        = Produto::find($item->cProd);
            $tributaProduto = TributacaoProduto::where(["natureza_operacao_id"=>$nfce->natureza_operacao_id,"produto_id"=>$produto->id])->first();
            if ($tributacaoPadrao) {
                $tributacao = $tributacaoPadrao;
            } else  if(!empty($tributaProduto->tributacao)){
                $tributacao =  $tributaProduto->tributacao;
            } else {
                $tributacao = $tributacao_geral;
            }
                        
            $item->cstICMS  = $tributacao->cstICMS;
            
            $item->numero_item  = $j++;
            calcularImposto($nfce, $item, $tributacao, $produto);
        }
    }
}

function calcularImposto($nfce, $item, $tributacao, $produto){
    //IPI
    if($item->cstIPI=="50"){
        $vBCIPI = BaseCalculoService::calculoVbcIpi($item, $tributacao);
        IpiService::calculo($item, $vBCIPI, $tributacao, $produto)  ;
    }
    
    //PIS
    $vBCPIS = BaseCalculoService::calculoVbcPis($item, $tributacao);
    PisService::calculo($item, $vBCPIS, $tributacao, $produto);
    
    // Confins
    $vBCCOFINS = BaseCalculoService::calculoVbcCofins($item, $tributacao, $produto);
    CofinsService::calculo($item, $vBCCOFINS, $tributacao, $produto);
    
    //ICMS
    $vBCICMS    = BaseCalculoService::calculoVbcIcms($item, $tributacao);
    $icms_estado= IcmsEstado::where(["uf_origem"=>$nfce->em_UF,"uf_destino"=>$nfce->em_UF])->first();
    $pICMS      = $icms_estado->aliquota_destino;
    if($produto->pICMS){
        $pICMS  = $produto->pICMS;
    }else if($tributacao->pICMS){
        $pICMS  = $tributacao->pICMS;
    }    
    
    if($tributacao->cstICMS=="10" || $tributacao->cstICMS=="30" || $tributacao->cstICMS=="70" || $tributacao->cstICMS=="201"
        || $tributacao->cstICMS=="202" || $tributacao->cstICMS=="203" || $tributacao->cstICMS=="900" || $tributacao->cstICMS=="500"){
            $iva = TributacaoIva::where(["tributacao_id" => $tributacao->id, "uf_origem"=>$nfce->em_UF,"uf_destino"=>$nfce->em_UF])->first();
            if(!$iva){
                $iva = TributacaoIva::where(["tributacao_id" => $tributacao->id, "uf_origem"=>$nfce->em_UF,"uf_destino"=>'TD'])->first();
            }
            //Se tiver o iva
            if($iva){
                $item->cstICMS = $iva->cstIcms ? $iva->cstIcms : $tributacao->cstICMS;
            }
            IcmsService::calculoICMS($item, $vBCICMS, $tributacao, $pICMS , $nfce->destinatario, $produto, $icms_estado, $iva);
    }else{
        IcmsService::calculoICMS($item, $vBCICMS, $tributacao, $pICMS , $nfce->destinatario, $produto);
    }
    
    $item->save();
}


function atualizarTotaisImpostosDaNota($id){
    $itens                  = NfceItem::where("nfce_id",$id);
    $nfce                   = Nfce::find($id);
    $total_itens            = $itens->sum("vProd");
    $nfce->vProd            = $total_itens;   
    $nfce->vFCP             = $itens->sum("vFCP");
    $nfce->vIPI             = $itens->sum("vIPI");
    $nfce->vPIS             = $itens->sum("vPIS");
    $nfce->vCOFINS          = $itens->sum("vCOFINS");
    $nfce->vBCST            = $itens->sum("vBCST");
    $nfce->vST              = $itens->sum("vICMSST");
    $nfce->vNF              = $nfce->vProd - $nfce->vDesc + $nfce->vST + $nfce->vFrete + $nfce->vSeg + $nfce->vOutro + $nfce->vII + $nfce->vIPI + $nfce->vServ;
    $nfce->vOrig            = $nfce->vNF;
    $nfce->vLiq             = $nfce->vOrig ;
    $nfce->estadual         = $itens->sum("estadual");
    $nfce->municipal        = $itens->sum("municipal");
    $nfce->nacionalfederal  = $itens->sum("nacionalfederal");
    $nfce->vTotTrib         = $itens->sum("vTotTrib");
    $nfce->vBC              = $itens->where("destaca_icms","S")->sum("vBCICMS");
    $nfce->vICMS            = $itens->where("destaca_icms","S")->sum("vICMS");
    $nfce->save();
}


