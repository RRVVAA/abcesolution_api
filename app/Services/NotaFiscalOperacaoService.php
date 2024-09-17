<?php
namespace App\Services;
use App\Models\Emitente;
use App\Models\Nfce;
use App\Models\Nfe;
use App\Models\NfeAutorizado;
use App\Models\NfeItem;
use App\Models\Tributacao;
use App\Models\TributacaoProduto;
use App\Models\NfceItem;
use App\Models\NfcePagamento;

class NotaFiscalOperacaoService{     
    
    public static function inserirNfcePelaVenda($venda, $natureza_operacao, $tributacao){        
        $emitente               = Emitente::where("empresa_id", $venda->empresa_id)->first();
        $retorno                = new \stdClass();        
        try {
            $nota               = new \stdClass();
            $nota->cUF          = ConstanteService::getUf($emitente->uf);
            $nota->natOp        = $natureza_operacao->descricao;
            $nota->venda_id     = $venda->id;
            $nota->empresa_id   = $venda->empresa_id;
            $nota->modelo       = config("constanteNota.mod.NFCE");
            $nota->nNF          = $emitente->ultimo_numero_nfe + 1;
            $nota->serie        = $emitente->numero_serie_nfe;
            $nota->cNF          = rand($nota->nNF,99999999);
            $nota->dhEmi        = hoje(). 'T'.date("H:i:s").'-03:00';
            $nota->dhSaiEnt     = hoje(). 'T'.date("H:i:s").'-03:00';
            $nota->tpNF         = config("constanteNota.tpNf.SAIDA"); //0 - Entrada / 1 - Saida
            
            //Verifica o destino da operação
            $nota->idDest       = config("constanteNota.idDest.INTERNA");            
           
            $nota->cMunFG       = $emitente->ibge;
            $nota->tpImp        = config("constanteNota.tpImp.DANFCE"); //formato do danfe
            $nota->tpEmis       = config("constanteNota.tpEmis.NORMAL") ; //tipo emissão - 1 - normal
            
            $nota->tpAmb        = $emitente->ambiente_nfce;
            $nota->finNFe       = config("constanteNota.finNFe.NORMAL") ; //Finalidade emissão 1 - Normal
            $nota->indFinal     = 1; // consumidor final
            $nota->indPres      = 1; //presença do comprador
            $nota->procEmi      = config("constanteNota.procEmi.APLICATIVO_CONTRIBUINTE");
            
            $nota->verProc      = '3.10.31';
            $nota->dhCont       = null;
            $nota->xJust        = null;
            $nota->indIntermed  = $emitente->indIntermed ? $emitente->indIntermed : null ;
            $nota->cnpjIntermed = ($emitente->cnpjIntermed) ? tira_mascara($emitente->cnpjIntermed) : null ;
            $nota->idCadIntTran = $emitente->idCadIntTran ? $emitente->idCadIntTran : null ;
            
            $nota->modFrete    = '9';
            $nota->em_xNome    = tiraAcento($emitente->razao_social);
            $nota->em_xFant    = tiraAcento($emitente->nome_fantasia);
            $nota->em_IE       = ($emitente->ie) ? tira_mascara($emitente->ie) : null ;
            $nota->em_IEST     = ($emitente->iest) ? tira_mascara($emitente->iest) : null;
            $nota->em_IM       = $emitente->im;
            $nota->em_CNAE     = ($emitente->cnae) ? tira_mascara($emitente->cnae) : null ;
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
            
            //Responsavel técnioco
            $nota->resp_CNPJ    = $emitente->resp_CNPJ;
            $nota->resp_xContato= $emitente->resp_xContato;
            $nota->resp_email   = $emitente->resp_email;
            $nota->resp_fone    = $emitente->resp_fone;
            $nota->resp_CSRT    = $emitente->resp_CSRT;
            $nota->resp_idCSRT  = $emitente->resp_idCSRT;
            
            //Destinatário
            $nota->cliente_cpf  =  ($venda->cliente_cpf) ? tira_mascara($venda->cliente_cpf) : null;
            $nota->cliente_nome =  ($venda->cliente_nome) ? tiraAcento($venda->cliente_nome) : null;
            $nota->indIEDest    =  ($venda->cliente_cpf) ? 9 : null;
           
            //Salvando a nota          
            $nota->status_id= config("constantes.status.DIGITACAO");
            $nfce            = Nfce::Create(objToArray($nota));
          
            $id_nfce         = $nfce->id;
            $emitente->ultimo_numero_nfe = $nota->nNF;
            $emitente->save();
           
            //Inserindo Itens
            $totalItens         = count($venda->itens);
            $itemCont           = 0;
            $somaFrete          = 0;
            $somaIPI            = 0;
            $somaProdutos       = 0;
            $qtd                = 1;
            foreach($venda->itens as $i){
                $tributaProduto = TributacaoProduto::where(["natureza_operacao_id"=>$natureza_operacao->id,"produto_id"=>$i->produto_id])->first();
                
                if($tributaProduto){
                    $tributacao =  $tributaProduto->tributacao;
                }else{
                    $tributacao = $tributacao;
                }
                
                $i->nfce_id     = $id_nfce;
                $i->numero_item = $qtd++;
                $i->cfop        = $tributacao->cfop ;
                $i->vFrete      = null;
                $i->vSeg        = null;
                $i->vDesc       = null;
                $i->vOutro      = null;
                
                if($venda->valor_desconto > 0){
                    if($itemCont < sizeof($venda->itens)){
                        $totalVenda = $venda->valor_venda;
                        $media = (((($i->vProd - $totalVenda)/$totalVenda))*100);
                        $media = 100 - ($media * -1);
                        
                        $tempDesc = ($venda->valor_desconto * $media)/100;
                        $somaDesconto += $tempDesc;
                        $i->vDesc = formataNumero($tempDesc);
                    }else{
                        $i->vDesc = formataNumero($venda->valor_desconto - $somaDesconto);
                    }
                }
                
                if($venda->frete){
                    if($venda->frete->valor > 0){
                        $somaFrete += $vFt = $venda->frete->valor/$totalItens;
                        $i->vFrete = formataNumero($vFt);
                    }
                }                                
                $somaProdutos   += $i->vProd;
                ItemNotafiscalService::inserir($i, $tributacao, $emitente );
            }
            
            $itens      = NfceItem::where("nfce_id",$id_nfce);
            $nfce       = Nfce::find($id_nfce);
            
            $total_itens= $itens->sum("vProd");
            $nfce->vOrig = $total_itens;
            $nfce->vLiq  = $nfce->vOrig - $nfce->vDesc;
            $nfce->vOrig = $total_itens;
            $nfce->vProd = $total_itens;
            
            $nfce->vBC   = $itens->sum("vBCICMS");
            $nfce->vICMS = $itens->sum("vICMS");
            $nfce->vFCP  = $itens->sum("vFCP");
            $nfce->vIPI  = $itens->sum("vIPI");
            $nfce->vPIS  = $itens->sum("vPIS");
            $nfce->vCOFINS  = $itens->sum("vCOFINS");
            $nfce->vNF   = $nfce->vOrig - $nfce->vDesc + $nfce->vST + $nfce->vFrete + $nfce->vSeg + $nfce->vOutro + $nfce->vII + $nfce->vIPI + $nfce->vServ;
            $nfce->save();
              
           //Duplicatas
           
           if(count($venda->pagamentos) > 0){
                $contFatura = 1;
                foreach($venda->pagamentos as $pg){
                    $dup        = new \stdClass();
                    $dup->nfce_id  = $id_nfce  ;
                    $dup->tPag  = zeroEsquerda($pg->forma_pagto_id,2)  ;
                    $dup->vPag  = formataNumero($pg->subtotal )  ;                 
                    
                    NfcePagamento::Create(objToArray($dup));
                    $contFatura++;
                }
            }
               
                
                if($emitente->autorizados){
                    foreach ($emitente->autorizados as $autorizado){
                        NfeAutorizado::Create(["nfe_id"=>$nfce->id,"aut_contato"=>$autorizado->aut_contato, "aut_cnpj"=>$autorizado->aut_cnpj]);
                    }
                }
                
                $retorno->tem_erro  = false;
                $retorno->erro      = "";
                $retorno->titulo    = "Nota Criada com sucesso!";
                $retorno->retorno   = $id_nfce;
                return $retorno;
        } catch (\Exception $e) {
            i($e->getMessage());
            $retorno->tem_erro  = true;
            $retorno->erro      = $e->getMessage();
            $retorno->titulo    = "Erro ao criar Nfe";
            $retorno->retorno   = null;
            return $retorno;
        }
        return $retorno;        
    }
    
    public static function salvarDadosNota($dados, $id){
        $nota = new \stdClass();
        if(($dados->venda_id ?? null) != null)
                $nota->venda_id	= $dados->venda_id;
        
            if(($dados->compra_id ?? null) != null)
                $nota->compra_id= $dados->compra_id;
        
            if(($dados->nota_referencia_id ?? null) != null)
                $nota->nota_referencia_id= $dados->nota_referencia_id;
        
            if(($dados->status_id ?? null) != null)
                $nota->status_id= $dados->status_id;
        
            if(($dados->natureza_operacao_id ?? null) != null)
                $nota->natureza_operacao_id= $dados->natureza_operacao_id; 
        
            if(($dados->chave ?? null) != null)
                $nota->chave= $dados->chave;
            
           if((($dados->procEmi ?? null ) != null) || $dados->procEmi ==0)
                $nota->procEmi= $dados->procEmi;     

            if(($dados->recibo ?? null) != null)
                $nota->recibo= $dados->recibo;
        
            if(($dados->protocolo ?? null) != null)
                $nota->protocolo= $dados->protocolo;   
        
            if(($dados->cUF ?? null) != null)
                $nota->cUF= $dados->cUF;
        
            if(($dados->cNF ?? null) != null)
                $nota->cNF= $dados->cNF;
        
            if(($dados->natOp ?? null) != null)
                $nota->natOp= $dados->natOp;
        
            if(($dados->modelo ?? null) != null)
                $nota->modelo= $dados->modelo;
        
            if(($dados->serie ?? null) != null)
                $nota->serie= $dados->serie;
        
            if(($dados->nNF ?? null) != null)
                $nota->nNF= $dados->nNF;
        
            if(($dados->sequencia_cce ?? null) != null)
                $nota->sequencia_cce= $dados->sequencia_cce;
        
            if(($dados->dhEmi ?? null) != null)
                $nota->dhEmi= $dados->dhEmi;
        
            if(($dados->dhSaiEnt ?? null) != null)
                $nota->dhSaiEnt= $dados->dhSaiEnt;
        
            if((($dados->tpNF ?? null ) != null) || $dados->tpNF ==0)
                $nota->tpNF= $dados->tpNF;
        
            if(($dados->idDest ?? null) != null)
                $nota->idDest= $dados->idDest;
        
            if(($dados->cMunFG ?? null) != null)
                $nota->cMunFG= $dados->cMunFG;
        
            if(($dados->tpImp ?? null) != null)
                $nota->tpImp= $dados->tpImp;
        
            if(($dados->tpEmis ?? null) != null)
                $nota->tpEmis= $dados->tpEmis;
        
            if(($dados->tpAmb ?? null) != null)
                $nota->tpAmb= $dados->tpAmb;
        
            if(($dados->finNFe ?? null) != null)
                $nota->finNFe= $dados->finNFe;
        
            if(($dados->indFinal ?? null) != null)
                $nota->indFinal= $dados->indFinal;
        
            if(($dados->indPres ?? null) != null)
                $nota->indPres= $dados->indPres;
        
            if(($dados->indIntermed ?? null) != null)
                $nota->indIntermed= $dados->indIntermed;
        
            if(($dados->cnpjIntermed ?? null) != null)
                $nota->cnpjIntermed= $dados->cnpjIntermed;
        
            if(($dados->idCadIntTran ?? null) != null)
                $nota->idCadIntTran= $dados->idCadIntTran;
        
            if(($dados->tipo_nota_referenciada ?? null) != null)
                $nota->tipo_nota_referenciada= $dados->tipo_nota_referenciada;
        
            if(($dados->ref_NFe ?? null) != null)
                $nota->ref_NFe= $dados->ref_NFe;
        
            if(($dados->ref_ano_mes ?? null) != null)
                $nota->ref_ano_mes= $dados->ref_ano_mes;
        
            if(($dados->ref_num_nf ?? null) != null)
                $nota->ref_num_nf= $dados->ref_num_nf;
        
            if(($dados->ref_serie ?? null) != null)
                $nota->ref_serie= $dados->ref_serie;
        
            if(($dados->procEmi ?? null) != null)
                $nota->procEmi= $dados->procEmi;
        
            if(($dados->verProc ?? null) != null)
                $nota->verProc= $dados->verProc;
        
            if(($dados->dhCont ?? null) != null)
                $nota->dhCont= $dados->dhCont;
        
            if(($dados->xJust ?? null) != null)
                $nota->xJust= $dados->xJust;
  
            if(($dados->vBC ?? null) != null)
                $nota->vBC= $dados->vBC;
        
            if(($dados->vICMS ?? null) != null)
                $nota->vICMS= $dados->vICMS;
        
            if(($dados->vICMSDeson ?? null) != null)
                $nota->vICMSDeson= $dados->vICMSDeson;
        
            if(($dados->vFCP ?? null) != null)
                $nota->vFCP= $dados->vFCP;
        
            if(($dados->vBCST ?? null) != null)
                $nota->vBCST= $dados->vBCST;
        
            if(($dados->vST ?? null) != null)
                $nota->vST= $dados->vST;
        
            if(($dados->vFCPST ?? null) != null)
                $nota->vFCPST= $dados->vFCPST;
        
            if(($dados->vFCPSTRet ?? null) != null)
                $nota->vFCPSTRet= $dados->vFCPSTRet;
        
            if(($dados->vProd ?? null) != null)
                $nota->vProd= $dados->vProd;
        
            if(($dados->vFrete ?? null) != null)
                $nota->vFrete= $dados->vFrete;
        
            if(($dados->vSeg ?? null) != null)
                $nota->vSeg= $dados->vSeg;
        
            if(($dados->vDesc ?? null) != null)
                $nota->vDesc= $dados->vDesc;
        
            if(($dados->vII ?? null) != null)
                $nota->vII= $dados->vII;
        
            if(($dados->vIPI ?? null) != null)
                $nota->vIPI= $dados->vIPI;
        
            if(($dados->vIPIDevol ?? null) != null)
                $nota->vIPIDevol= $dados->vIPIDevol;
        
            if(($dados->vPIS ?? null) != null)
                $nota->vPIS= $dados->vPIS;
        
            if(($dados->vCOFINS ?? null) != null)
                $nota->vCOFINS= $dados->vCOFINS;
        
            if(($dados->vOutro ?? null) != null)
                $nota->vOutro= $dados->vOutro;
        
            if(($dados->vNF ?? null) != null)
                $nota->vNF= $dados->vNF;
        
            if(($dados->vTotTrib ?? null) != null)
                $nota->vTotTrib= $dados->vTotTrib;
        
            if(($dados->vOrig ?? null) != null)
                $nota->vOrig= $dados->vOrig;
        
            if(($dados->vLiq ?? null) != null)
                $nota->vLiq= $dados->vLiq;
        
            if(($dados->vTroco ?? null) != null)
                $nota->vTroco= $dados->vTroco;
        
            if(($dados->nFat ?? null) != null)
                $nota->nFat= $dados->nFat;
                
       
            
            if(($dados->em_xNome ?? null) != null)
                $nota->em_xNome= $dados->em_xNome;
        
            if(($dados->em_xFant ?? null) != null)
                $nota->em_xFant= $dados->em_xFant;
        
            if(($dados->em_IE ?? null) != null)
                $nota->em_IE= $dados->em_IE;
        
            if(($dados->em_IEST ?? null) != null)
                $nota->em_IEST= $dados->em_IEST;
        
            if(($dados->em_IM ?? null) != null)
                $nota->em_IM= $dados->em_IM;
        
            if(($dados->em_CNAE ?? null) != null)
                $nota->em_CNAE= $dados->em_CNAE;
        
            if(($dados->em_CRT ?? null) != null)
                $nota->em_CRT= $dados->em_CRT;
        
            if(($dados->em_CNPJ ?? null) != null)
                $nota->em_CNPJ= $dados->em_CNPJ;
        
            if(($dados->em_CPF ?? null) != null)
                $nota->em_CPF= $dados->em_CPF;
        
            if(($dados->em_xLgr ?? null) != null)
                $nota->em_xLgr= $dados->em_xLgr;
        
            if(($dados->em_nro ?? null) != null)
                $nota->em_nro= $dados->em_nro;
        
            if(($dados->em_xCpl ?? null) != null)
                $nota->em_xCpl= $dados->em_xCpl;
        
            if(($dados->em_xBairro ?? null) != null)
                $nota->em_xBairro= $dados->em_xBairro;
        
            if(($dados->em_cMun ?? null) != null)
                $nota->em_cMun= $dados->em_cMun;
        
            if(($dados->em_xMun ?? null) != null)
                $nota->em_xMun= $dados->em_xMun;
        
            if(($dados->em_UF ?? null) != null)
                $nota->em_UF= $dados->em_UF;
        
            if(($dados->em_CEP ?? null) != null)
                $nota->em_CEP= $dados->em_CEP;
        
            if(($dados->em_cPais ?? null) != null)
                $nota->em_cPais= $dados->em_cPais;
        
            if(($dados->em_xPais ?? null) != null)
                $nota->em_xPais= $dados->em_xPais;
        
            if(($dados->em_fone ?? null) != null)
                $nota->em_fone= $dados->em_fone;
        
            if(($dados->em_EMAIL ?? null) != null)
                $nota->em_EMAIL= $dados->em_EMAIL;
        
            if(($dados->em_SUFRAMA ?? null) != null)
                $nota->em_SUFRAMA= $dados->em_SUFRAMA;


            if(($dados->modFrete ?? null) != null)
                $nota->modFrete= $dados->modFrete;
        
            if(($dados->tPag ?? null) != null)
                $nota->tPag= $dados->tPag;
        
            if(($dados->vPag ?? null) != null)
                $nota->vPag= $dados->vPag;
        
            if(($dados->CNPJ_pag ?? null) != null)
                $nota->CNPJ_pag= $dados->CNPJ_pag;
        
            if(($dados->tBand ?? null) != null)
                $nota->tBand= $dados->tBand;
        
            if(($dados->cAut ?? null) != null)
                $nota->cAut= $dados->cAut;
        
            if(($dados->tpIntegra ?? null) != null)
                $nota->tpIntegra= $dados->tpIntegra;
        
            if(($dados->indPag ?? null) != null)
                $nota->indPag= $dados->indPag;
        
            if(($dados->infAdFisco ?? null) != null)
                $nota->infAdFisco= $dados->infAdFisco;
        
            if(($dados->infCpl ?? null) != null)
                $nota->infCpl = $dados->infCpl;
        
            if(($dados->resp_CNPJ ?? null) != null)            
                $nota->resp_CNPJ= $dados->resp_CNPJ;
        
            if(($dados->resp_xContato ?? null) != null)
                $nota->resp_xContato= $dados->resp_xContato;
        
            if(($dados->resp_email ?? null) != null)
                $nota->resp_email= $dados->resp_email;
        
            if(($dados->resp_fone ?? null) != null)
                $nota->resp_fone= $dados->resp_fone;
        
            if(($dados->resp_CSRT ?? null) != null)
                $nota->resp_CSRT= $dados->resp_CSRT;
        
            if(($dados->resp_idCSRT ?? null) != null)
                $nota->resp_idCSRT= $dados->resp_idCSRT;        
            
        if($id){
            Nfe::where("id", $id)->update(objToArray($nota));
            $nfe = Nfe::find($id);
        }else{
            $nfe = Nfe::Create(objToArray($nota));
            $emitente = Emitente::where("empresa_id", $dados->empresa_id)->first();
            $emitente->ultimo_numero_nfe = $nota->nNF;
            $emitente->save();
            
            if($emitente->autorizados){
                foreach ($emitente->autorizados as $autorizado){
                    NfeAutorizado::Create(["nfe_id"=>$nfe->id,"aut_contato"=>$autorizado->aut_contato, "aut_cnpj"=>$autorizado->aut_cnpj]);
                }
            }
           
        } 
        
        
        return $nfe;       
    }
    
    public static function atualizarTotaisDaNota($id){
        $itens      = NfeItem::where("nfe_id",$id);
        $nfe        = Nfe::find($id);
        
        $total_itens= $itens->sum("vProd");
        $nfe->vOrig = $total_itens;
        $nfe->vLiq  = $nfe->vOrig - $nfe->vDesc;
        $nfe->vOrig = $total_itens;
        $nfe->vProd = $total_itens;
        
        $nfe->vBC   = $itens->sum("vBCICMS");
        $nfe->vICMS = $itens->sum("vICMS");
        $nfe->vFCP  = $itens->sum("vFCP");
        $nfe->vIPI  = $itens->sum("vIPI");
        $nfe->vPIS  = $itens->sum("vPIS");
        $nfe->vCOFINS  = $itens->sum("vCOFINS");        
        $nfe->vNF   = $nfe->vOrig - $nfe->vDesc + $nfe->vST + $nfe->vFrete + $nfe->vSeg + $nfe->vOutro + $nfe->vII + $nfe->vIPI + $nfe->vServ;
        
        
        $nfe->save();         
    }
    
    public static function atualizarValoresDaNota($nfe_id){
        $nfe                = Nfe::find($nfe_id);
        $emitente           = Emitente::where("empresa_id", $nfe->empresa_id)->first();
        $tributacao_geral   = Tributacao::where(["natureza_operacao_id" =>$nfe->natureza_operacao_id,"padrao"=>"S"])->first();
        $itens              = NfeItem::where("nfe_id", $nfe_id);
        
        foreach($itens->get() as $item){
            $tributaProduto = TributacaoProduto::where(["natureza_operacao_id"=>$nfe->natureza_operacao_id,"produto_id"=>$item->produto_id])->first();
            
            if($tributaProduto){
                $tributacao =  $tributaProduto->tributacao;
            }else{
                $tributacao = $tributacao_geral;
            }
            $vBC      = $item->qCom * $item->vUnCom;
            //IPI
            IpiService::calculo($item, $vBC, $tributacao)  ;
            //PIS
            PisService::calculo($item, $vBC, $tributacao);
            // Confins
            CofinsService::calculo($item,$vBC, $tributacao);
            //ICMS
            IcmsService::calculoIcmsSn($item, $tributacao, $emitente);
           
        }
        $total_itens= $itens->sum("vProd");
        $nfe->vOrig = $total_itens;
        $nfe->vLiq  = $nfe->vOrig - $nfe->vDesc;
        $nfe->vOrig = $total_itens;
        $nfe->vProd = $total_itens;
        $nfe->vNF   = $nfe->vOrig - $nfe->vDesc + $nfe->vST + $nfe->vFrete + $nfe->vSeg + $nfe->vOutro + $nfe->vII + $nfe->vIP + $nfe->vServ;
        $nfe->save();
    }
    
  
}

