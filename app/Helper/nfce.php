<?php

use App\Models\Emitente;
use App\Models\Nfce;
use App\Models\NfceDuplicata;
use App\Models\NfceItem;
use App\Services\ConstanteService;

function inserirNfcePelaVenda($pdvVenda, $natureza_operacao, $tributacao){
    $emitente           = Emitente::where("empresa_id", $pdvVenda->empresa_id)->first();
    
    $nota               = new \stdClass();
    $nota->cUF          = ConstanteService::getUf($emitente->uf);
    $nota->natOp        = $natureza_operacao->descricao;
    $nota->natureza_operacao_id = $natureza_operacao->id;
    
    $nota->empresa_id   = $pdvVenda->empresa_id;
    $nota->pdvvenda_id  = $pdvVenda->id;
    $nota->modelo       = config("constanteNota.mod.NFCE");
  
    $nota->dhEmi        = hoje(). 'T'.date("H:i:s").'-03:00';
    $nota->dhSaiEnt     = hoje(). 'T'.date("H:i:s").'-03:00';
    $nota->tpNF         = config("constanteNota.tpNf.SAIDA"); //0 - Entrada / 1 - Saida
    $nota->idDest       = config("constanteNota.idDest.INTERNA");
    
    $nota->cMunFG       = $emitente->ibge;
    $nota->tpImp        = config("constanteNota.tpImp.DANFCE");  //formato do danfce
    $nota->tpEmis       = config("constanteNota.tpEmis.NORMAL"); //tipo emissão - 1 - normal
    
    $nota->tpAmb        = $emitente->ambiente_nfce;
    $nota->finNFe       = config("constanteNota.finNFe.NORMAL") ; //Finalidade emissão 1 - Normal
    $nota->indFinal     = config("constanteNota.indFinal.CONSUMIDOR_FINAL"); // consumidor final
    $nota->indPres      = config("constanteNota.indPres.PRESENCIAL"); //presença do comprador
    $nota->procEmi      = config("constanteNota.procEmi.APLICATIVO_CONTRIBUINTE");
    $nota->verProc      = '4.0';
    $nota->dhCont       = null;
    $nota->xJust        = null;
    
    $nota->desconto_nota= $pdvVenda->valor_desconto;
    $nota->vDesc        = $pdvVenda->valor_desconto;
    $nota->vOutro       = $pdvVenda->valor_acrescimo;
    
    $nota->modFrete    = '9';
    $nota->em_xNome    = tiraAcento($emitente->razao_social);
    $nota->em_xFant    = tiraAcento($emitente->nome_fantasia);
    $nota->em_IE       = ($emitente->ie) ? tira_mascara($emitente->ie) : null ;
    $nota->em_IEST     = ($emitente->iest) ? tira_mascara($emitente->iest) : null;
    $nota->em_IM       = $emitente->im;
    $nota->em_CNAE     = $emitente->cnae;
    $nota->em_CRT      = $emitente->crt;
    $cnpj  = ($emitente->cnpj) ? tira_mascara($emitente->cnpj) : null;
    if(strlen($cnpj) == 14){
        $nota->em_CNPJ = $cnpj;
    }else{
        $nota->em_CPF = $cnpj;
    }
    
    $nota->em_xLgr     = tiraAcento($emitente->logradouro);
    $nota->em_nro      = $emitente->numero;
    $nota->em_xCpl     = tiraAcento($emitente->complemento);
    $nota->em_xBairro  = tiraAcento($emitente->bairro);
    $nota->em_cMun     = $emitente->ibge;
    $nota->em_xMun     = tiraAcento($emitente->cidade);
    $nota->em_UF       = $emitente->uf;
    $nota->em_CEP      = ($emitente->cep) ? tira_mascara($emitente->cep) : null;
    $nota->em_cPais    = "1058";
    $nota->em_xPais    = "Brasil";
    $nota->em_fone     = ($emitente->fone) ? tira_mascara($emitente->fone): null;
    
    /*$nota->indIntermed  = $emitente->indIntermed;
     $nota->cnpjIntermed = $emitente->cnpjIntermed ? tira_mascara($emitente->cnpjIntermed) : null ;
     $nota->idCadIntTran = $emitente->idCadIntTran;*/
    
    //Responsavel técnioco
    $nota->resp_CNPJ    = $emitente->resp_CNPJ;
    $nota->resp_xContato= $emitente->resp_xContato;
    $nota->resp_email   = $emitente->resp_email;
    $nota->resp_fone    = $emitente->resp_fone;
    $nota->resp_CSRT    = $emitente->resp_CSRT;
    $nota->resp_idCSRT  = $emitente->resp_idCSRT;
    
    //Destinatário
    $nota->cliente_cpf  =  ($pdvVenda->cliente_cpf) ? tira_mascara($pdvVenda->cliente_cpf) : null;
    $nota->cliente_cnpj =  ($pdvVenda->cliente_cnpj) ? tiraAcento($pdvVenda->cliente_cnpj) : null;
    $nota->indIEDest    =  ($pdvVenda->cliente_cpf) ? 9 : null;
    
    $nfce               = Nfce::where("pdvvenda_id", $pdvVenda->id)->first();
    if(!$nfce){
        $nota->serie        = $emitente->numero_serie_nfce;
        $nota->nNF          = $emitente->ultimo_numero_nfce + 1;
        $nota->cNF          = rand($nota->nNF,99999999);
        $nota->status_id    = config("constantes.status.DIGITACAO");
        $nfce               = Nfce::Create(objToArray($nota));
        $id_nfce            = $nfce->id;
        $emitente->ultimo_numero_nfce = $nota->nNF;
        $emitente->save();
    }else{
        $nfce->update(objToArray($nota));
        $id_nfce         = $nfce->id;
    }
    
    NfceItem::where("nfce_id", $id_nfce)->delete();
    foreach($pdvVenda->itens as $i){
        $item                  = new \stdClass();
        $item->nfce_id         = $id_nfce;
        $item->qCom            = $i->qtde;
        $item->cProd           = $i->produto_id;
        $item->preco_original  = $i->valor ;
        
       
        //$item->subtotal_liquido= $i->subtotal_liquido;
        $item->desconto_por_unidade     = $i->desconto_por_unidade;
        $item->total_desconto_item      = $i->total_desconto_item;
        $item->desconto_percentual      = $i->desconto_percentual;
        $item->desconto_por_valor       = $i->desconto_por_valor;
        $item->subtotal_liquido         = $i->subtotal_liquido ;
        
        $item->vUnCom                   = $item->preco_original  - $item->desconto_por_unidade;
        $item->vProd                    = $item->qCom * $item->preco_original - ($item->desconto_por_unidade * $item->qCom) ;
        NfceItem::create(objToArray($item));
    }
    
    //Duplicata
    NfceDuplicata::where("nfce_id", $id_nfce)->delete();
    if(count($pdvVenda->duplicatas) > 0){
        $contFatura = 1;
        foreach($pdvVenda->duplicatas as $ft){
            $dup        = new \stdClass();
            $dup->nfce_id = $id_nfce;
            $dup->nDup  = $ft->nDup;
            $dup->dVenc = $ft->dVenc;
            $dup->vDup  = $ft->vDup;
            $dup->troco  = $ft->troco;
            $dup->tPag  = $ft->tPag;
            $dup->obs   = $ft->obs;
            NfceDuplicata::Create(objToArray($dup));
            $contFatura++;
        }
    }
    
    return $id_nfce;
}
