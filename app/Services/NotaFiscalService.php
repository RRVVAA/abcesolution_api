<?php

namespace App\Services;

use App\Models\Nfce;
use App\Models\NfceDuplicata;
use App\Models\Nfe;
use App\Models\NfeDuplicata;


class NotaFiscalService
{
    public static function prepararNfe($id_nfe, $modelo = 55)
    {
        if ($modelo == 55) {
            $nfe = Nfe::where("id", $id_nfe)->first();
        } elseif ($modelo == 65) {
            $nfe = Nfce::where("id", $id_nfe)->first();
        }

        $nota = new \stdClass();
        $nota->id = $nfe->id;
        $nota->venda_id = $nfe->venda_id;
        $nota->empresa_id = $nfe->empresa_id;
        $nota->status_id = $nfe->status_id;
        $nota->chave = $nfe->chave;
        $nota->recibo = $nfe->recibo;
        $nota->protocolo = $nfe->protocolo;
        $nota->sequencia_cce = $nfe->sequencia_cce;

        $nota->cUF = $nfe->cUF;
        $nota->natOp = $nfe->natOp;
        $nota->serie = $nfe->serie;
        $nota->nNF = $nfe->nNF;
        $nota->cNF = $nfe->cNF;
        $nota->tpNF = $nfe->tpNF;
        $nota->idDest = $nfe->idDest;
        $nota->tpImp = $nfe->tpImp;
        $nota->tpEmis = $nfe->tpEmis;
        $nota->tpAmb = $nfe->tpAmb;
        $nota->finNFe = $nfe->finNFe;
        $nota->indFinal = $nfe->indFinal;
        $nota->indPres = $nfe->indPres;
        $nota->procEmi = $nfe->procEmi;
        $nota->verProc = $nfe->verProc;
        $nota->modFrete = $nfe->modFrete;

        //Intermediário
        $nota->indIntermed = $nfe->indIntermed;
        $nota->cnpjIntermed = $nfe->cnpjIntermed;
        $nota->idCadIntTran = $nfe->idCadIntTran;

        //Responsável técnico
        $nota->CNPJ = $nfe->resp_CNPJ; //CNPJ da pessoa jurídica responsável pelo sistema utilizado na emissão do documento fiscal eletrônico
        $nota->xContato = $nfe->resp_xContato; //Nome da pessoa a ser contatada
        $nota->email = $nfe->resp_email; //E-mail da pessoa jurídica a ser contatada
        $nota->fone = $nfe->resp_fone; //Telefone da pessoa jurídica/física a ser contatada
        $nota->CSRT = $nfe->resp_CSRT; //Código de Segurança do Responsável Técnico
        $nota->idCSRT = $nfe->resp_idCSRT; //Identificador do CSRT

        $nota->tipo_nota_referenciada = $nfe->tipo_nota_referenciada; //Identificador do CSRT
        $nota->ref_NFe = $nfe->ref_NFe; //Identificador do CSRT
        $nota->ref_ano_mes = $nfe->ref_ano_mes; //Identificador do CSRT
        $nota->ref_num_nf = $nfe->ref_num_nf; //Identificador do CSRT
        $nota->ref_serie = $nfe->ref_serie; //Identificador do CSRT

        //Emitente
        $emitente = new \stdClass();
        $emitente->xNome = $nfe->em_xNome;
        $emitente->xFant = $nfe->em_xFant;
        $emitente->IE = $nfe->em_IE;
        $emitente->IEST = $nfe->em_IEST;
        $emitente->IM = $nfe->em_IM;
        $emitente->CNAE = $nfe->em_CNAE;
        $emitente->CRT = $nfe->em_CRT;
        $emitente->CNPJ = $nfe->em_CNPJ; //indicar apenas um CNPJ ou CPF
        $emitente->CPF = $nfe->em_CPF;
        $emitente->xLgr = $nfe->em_xLgr;
        $emitente->nro = $nfe->em_nro;
        $emitente->xCpl = $nfe->em_xCpl;
        $emitente->xBairro = $nfe->em_xBairro;
        $emitente->cMun = $nfe->em_cMun;
        $emitente->xMun = $nfe->em_xMun;
        $emitente->UF = $nfe->em_UF;
        $emitente->CEP = $nfe->em_CEP;
        $emitente->cPais = $nfe->em_cPais;
        $emitente->xPais = $nfe->em_xPais;
        $emitente->fone = $nfe->em_fone;


        $destinatario = new \stdClass();

        if ($nfe->destinatario) {
            $objDest = $nfe->destinatario;

            $destinatario->xNome = $objDest->dest_xNome;
            $destinatario->CNPJ = $objDest->dest_CNPJ;
            $destinatario->CPF = $objDest->dest_CPF;
            $destinatario->idEstrangeiro = $objDest->dest_idEstrangeiro;
            $destinatario->indIEDest = $objDest->dest_indIEDest;
            $destinatario->IE = $objDest->dest_IE;
            $destinatario->ISUF = $objDest->dest_ISUF;
            $destinatario->IM = $objDest->dest_IM;
            $destinatario->email = $objDest->dest_email;
            $destinatario->xLgr = $objDest->dest_xLgr;
            $destinatario->nro = $objDest->dest_nro;
            $destinatario->xCpl = $objDest->dest_xCpl;
            $destinatario->xBairro = $objDest->dest_xBairro;
            $destinatario->cMun = $objDest->dest_cMun;
            $destinatario->xMun = $objDest->dest_xMun;
            $destinatario->UF = $objDest->dest_UF;
            $destinatario->CEP = $objDest->dest_CEP;
            $destinatario->cPais = $objDest->dest_cPais;
            $destinatario->xPais = $objDest->dest_xPais;
            $destinatario->fone = $objDest->dest_fone;
        }

        $itens = array();
        if (count($nfe->itens) > 0) {
            foreach ($nfe->itens as $item) {
                $produto = new \stdClass();

                $produto->item = $item->numero_item;
                $produto->cProd = $item->cProd;
                $produto->cEAN = $item->cEAN;
                $produto->xProd = $item->xProd;
                $produto->NCM = $item->NCM;
                $produto->cBenef = $item->cBenef;
                $produto->EXTIPI = $item->EXTIPI;
                $produto->CFOP = $item->CFOP;
                $produto->CEST = $item->CEST;
                $produto->uCom = $item->uCom;
                $produto->qCom = $item->qCom;
                $produto->vUnCom = $item->vUnCom;
                $produto->vProd = $item->vProd;
                $produto->cEANTrib = $item->cEANTrib;
                $produto->uTrib = $item->uTrib;
                $produto->qTrib = $item->qTrib;
                $produto->vUnTrib = $item->vUnTrib;
                $produto->vFrete = $item->vFrete;
                $produto->vSeg = $item->vSeg;
                $produto->vDesc = $item->vDesc;
                $produto->vOutro = $item->vOutro;
                $produto->indTot = $item->indTot;
                $produto->xPed = $item->xPed;
                $produto->nItemPed = $item->nItemPed;
                $produto->nFCI = $item->nFCI;
                $produto->vTotTrib = $item->vTotTrib;

                $observacao = new \stdClass();
                //$observacao->item =1; //item da NFe
                //$observacao->infAdProd = 'informacao adicional do item';

                $imposto = new \stdClass();
                //$imposto->item       = 1; //item da NFe
                // $imposto->vTotTrib   = null;


                $icms = new \stdClass();
                $icms->item = $item->numero_item; //item da NFe
                $icms->orig = $item->orig;
                $calcula_icms = true;
                if (intVal($item->cstICMS) > 100) {
                    $icms->CSOSN = $item->cstICMS;
                    if ($item->cstICMS != "900") {
                        $calcula_icms = false;
                    }
                    unset($icms->CST);

                } else {
                    $icms->CST = $item->cstICMS;
                }


                $icms->pCredSN = $item->pCredSN;
                $icms->vCredICMSSN = $item->vCredICMSSN;
                $icms->modBCST = $item->modBCST;
                $icms->pMVAST = $item->pMVAST;
                $icms->pRedBCST = $item->pRedBCST;
                $icms->vBCST = $item->vBCST;
                $icms->pICMSST = $item->pICMSST;
                $icms->vICMSST = $item->vICMSST;
                $icms->vBCFCPST = $item->vBCFCPST; //incluso no layout 4.00
                $icms->pFCPST = $item->pFCPST; //incluso no layout 4.00
                $icms->vFCPST = $item->vFCPST; //incluso no layout 4.00
                $icms->vBCSTRet = $item->vBCSTRet;
                $icms->pST = $item->pST;
                $icms->vICMSSTRet = $item->vICMSSTRet;
                $icms->vBCFCPSTRet = $item->vBCFCPSTRet; //incluso no layout 4.00
                $icms->pFCPSTRet = $item->pFCPSTRet; //incluso no layout 4.00
                $icms->vFCPSTRet = $item->vFCPSTRet; //incluso no layout 4.00
                $icms->modBC = $item->modBC;
                $icms->vBC = $item->vBCICMS;
                $icms->pRedBC = $item->pRedBC;
                $icms->pICMS = $item->pICMS;
                $icms->vICMS = $item->vICMS;
                $icms->pRedBCEfet = $item->pRedBCEfet;
                $icms->vBCEfet = $item->vBCEfet;
                $icms->pICMSEfet = $item->pICMSEfet;
                $icms->vICMSEfet = $item->vICMSEfet;
                $icms->vICMSSubstituto = $item->vICMSSubstituto;
                $icms->modBC = $item->modBC;
                $icms->vBC = $item->vBCICMS;
                $icms->pICMS = $item->pICMS;
                $icms->vICMS = $item->vICMS;


                if (!$calcula_icms) {
                    $icms->vBC = 0;
                    $icms->pICMS = 0;
                    $icms->vICMS = 0;
                }
                $icms->pFCP = $item->pFCP;
                $icms->vFCP = $item->vFCP;
                $icms->vBCFCP = $item->vBCFCP;
                $icms->modBCST = $item->modBCST;
                $icms->pMVAST = $item->pMVAST;
                $icms->pRedBCST = $item->pRedBCST;
                $icms->vBCST = $item->vBCST;
                $icms->pICMSST = $item->pICMSST;
                $icms->vICMSST = $item->vICMSST;
                $icms->vBCFCPST = $item->vBCFCPST;
                $icms->pFCPST = $item->pFCPST;
                $icms->vFCPST = $item->vFCPST;
                $icms->vICMSDeson = $item->vICMSDeson;
                $icms->motDesICMS = $item->motDesICMS;
                $icms->pRedBC = $item->pRedBC;
                $icms->vICMSOp = $item->vICMSOp;
                $icms->pDif = $item->pDif;
                $icms->vICMSDif = $item->vICMSDif;
                $icms->vBCSTRet = $item->vBCSTRet;
                $icms->pST = $item->pST;
                $icms->vICMSSTRet = $item->vICMSSTRet;
                $icms->vBCFCPSTRet = $item->vBCFCPSTRet;
                $icms->pFCPSTRet = $item->pFCPSTRet;
                $icms->vFCPSTRet = $item->vFCPSTRet;
                $icms->pRedBCEfet = $item->pRedBCEfet;
                $icms->vBCEfet = $item->vBCEfet;
                $icms->pICMSEfet = $item->pICMSEfet;
                $icms->vICMSEfet = $item->vICMSEfet;
                $icms->vICMSSubstituto = $item->vICMSSubstituto; //NT 2020.005 v1.20
                $icms->vICMSSTDeson = $item->vICMSSTDeson; //NT 2020.005 v1.20
                $icms->motDesICMSST = $item->motDesICMSST; //NT 2020.005 v1.20
                $icms->pFCPDif = $item->pFCPDif; //NT 2020.005 v1.20
                $icms->vFCPDif = $item->vFCPDif; //NT 2020.005 v1.20
                $icms->vFCPEfet = $item->vFCPEfet; //NT 2020.005 v1.20


                $ipi = new \stdClass();
                $ipi->item = $item->numero_item; //item da NFe
                $ipi->clEnq = $item->clEnq;
                $ipi->CNPJProd = $item->CNPJProd;
                $ipi->cSelo = $item->cSelo;
                $ipi->qSelo = $item->qSelo;
                $ipi->cEnq = $item->cEnq;
                $ipi->CST = $item->cstIPI;
                $ipi->vIPI = $item->vIPI;
                $ipi->vBC = $item->vBCIPI;
                $ipi->pIPI = $item->pIPI;
                $ipi->qUnid = $item->qUnidIPI;
                $ipi->vUnid = $item->vUnidIPI;

                $pis = new \stdClass();
                $pis->item = $item->numero_item; //item da NFe
                $pis->CST = $item->cstPIS;
                $pis->vBC = $item->vBCPIS;
                $pis->pPIS = $item->pPIS;
                $pis->vPIS = $item->vPIS;
                $pis->qBCProd = $item->qBCProdPis;
                $pis->vAliqProd = $item->vAliqProd_pis;

                $cofins = new \stdClass();
                $cofins->item = $item->numero_item; //item da NFe
                $cofins->CST = $item->cstCOFINS;
                $cofins->vBC = $item->vBCCOFINS;
                $cofins->pCOFINS = $item->pCOFINS;
                $cofins->vCOFINS = $item->vCOFINS;
                $cofins->qBCProd = $item->qBCProdConfis;
                $cofins->vAliqProd = $item->vAliqProd_cofins;

                $itens[] = array(
                    "produto" => $produto,
                    "observacao" => $observacao,
                    "imposto" => $imposto,
                    "icms" => ($icms) ?? null,
                    "ipi" => $ipi,
                    "pis" => $pis,
                    "cofins" => $cofins
                );

            }
        }

        $nota->vBC = $nfe->vBC;
        $nota->vICMS = $nfe->vICMS;
        $nota->vICMSDeson = $nfe->vICMSDeson;
        $nota->vFCP = $nfe->vFCP; //incluso no layout 4.00
        $nota->vBCST = $nfe->vBCST;
        $nota->vST = $nfe->vST;
        $nota->vFCPST = $nfe->vFCPST; //incluso no layout 4.00
        $nota->vFCPSTRet = $nfe->vFCPSTRet; //incluso no layout 4.00
        $nota->vProd = $nfe->vProd;
        $nota->vFrete = $nfe->vFrete;
        $nota->vSeg = $nfe->vSeg;
        $nota->vDesc = $nfe->vDesc;
        $nota->vII = $nfe->vII;
        $nota->vIPI = $nfe->vIPI;
        $nota->vIPIDevol = $nfe->vIPIDevol; //incluso no layout 4.00
        $nota->vPIS = $nfe->vPIS;
        $nota->vCOFINS = $nfe->vCOFINS;
        $nota->vOutro = $nfe->vOutro;
        $nota->vNF = $nfe->vNF;
        $nota->vTotTrib = $nfe->vTotTrib;

        $nota->infAdFisco = $nfe->infAdFisco;
        $nota->infCpl = $nfe->infCpl;


        $nota->transp_xNome = $nfe->transp_xNome;
        $nota->transp_IE = $nfe->transp_IE;
        $nota->transp_xEnder = $nfe->transp_xEnder;
        $nota->transp_xMun = $nfe->ttransp_xMun;
        $nota->transp_UF = $nfe->transp_UF;
        $nota->transp_CNPJ = $nfe->transp_CNPJ;//só pode haver um ou CNPJ ou CPF, se um deles é especificado o outro deverá ser null
        $nota->transp_CPF = $nfe->transp_CPF;

        $nota->qVol = $nfe->qVol;
        $nota->esp = $nfe->esp;
        $nota->marca = $nfe->marca;
        $nota->nVol = $nfe->nVol;
        $nota->pesoL = $nfe->pesoL;
        $nota->pesoB = $nfe->pesoB;

        $nota->transp_ret_vServ = $nfe->transp_ret_vServ;
        $nota->transp_ret_vBCRet = $nfe->transp_ret_vBCRet;
        $nota->transp_ret_pICMSRet = $nfe->transp_ret_pICMSRet;
        $nota->transp_ret_CFOP = $nfe->transp_ret_CFOP;
        $nota->transp_ret_cMunFG = $nfe->transp_ret_cMunFG;
        $nota->transp_veic_placa = $nfe->transp_veic_placa;

        $nota->transp_veic_UF = $nfe->transp_veic_UF;
        $nota->transp_veic_RNTC = $nfe->transp_veic_RNTC;
        $nota->transp_veic_placa = $nfe->transp_veic_placa;

        $nota->transp_reboque_placa = $nfe->transp_reboque_placa;
        $nota->transp_reboque_UF = $nfe->transp_reboque_UF;
        $nota->transp_reboque_RNTC = $nfe->transp_reboque_RNTC;


        /* if($nfe->transporte){
             $transportadora        = new \stdClass();
             $transportadora->xNome = $nfe->transporte->transp_xNome;
             $transportadora->IE    = $nfe->transporte->transp_IE;
             $transportadora->xEnder= $nfe->transporte->transp_xEnder;
             $transportadora->xMun  = $nfe->transporte->transp_xMun;
             $transportadora->UF    = $nfe->transporte->transp_UF;
             $transportadora->CNPJ  = $nfe->transporte->transp_CNPJ;//só pode haver um ou CNPJ ou CPF, se um deles é especificado o outro deverá ser null
             $transportadora->CPF   = $nfe->transporte->transp_CPF;

             $transportadora->qVol   = $nfe->transporte->qVol;
             $transportadora->esp   = $nfe->transporte->esp;
             $transportadora->marca   = $nfe->transporte->marca;
             $transportadora->nVol   = $nfe->transporte->nVol;
             $transportadora->pesoL   = $nfe->transporte->pesoL;
             $transportadora->pesoB   = $nfe->transporte->pesoB;


         }*/

        $fatura = new \stdClass();


        $duplicatas = array();
        if ($nfe->duplicatas) {
            foreach ($nfe->duplicatas as $dup) {
                $duplicata = new \stdClass();
                $duplicata->nDup = $dup->nDup;
                $duplicata->dVenc = $dup->dVenc;
                $duplicata->vDup = $dup->vDup;
                $duplicatas[] = $duplicata;
            }
        }

        $cobranca = new \stdClass();
        $cobranca->nFat = $nfe->nFat;
        $cobranca->vOrig = $nfe->vOrig;
        $cobranca->vDesc = $nfe->vDesc;
        $cobranca->vLiq = $nfe->vLiq;
        $cobranca->duplicatas = $duplicatas;


        if ($nfe->autorizados) {
            $autorizados = array();
            foreach ($nfe->autorizados as $autorizado) {
                $aut = new \stdClass();
                $aut->CNPJ = null;
                $aut->CPF = null;
                $cnpj = ($autorizado->aut_cnpj) ? tira_mascara($autorizado->aut_cnpj) : null;
                if (strlen($cnpj) == 14) {
                    $aut->CNPJ = $cnpj;
                } else {
                    $aut->CPF = $cnpj;
                }

                $autorizados[] = $aut;
            }
        }

        if ($nfe->referenciados) {
            $referenciados = array();
            foreach ($nfe->referenciados as $ref) {
                $referenciado = new \stdClass();
                $referenciado->tipo_nota_referenciada = $ref->tipo_nota_referenciada;
                $referenciado->ref_NFe = $ref->ref_NFe;
                $referenciado->ref_ano_mes = $ref->ref_ano_mes;
                $referenciado->ref_num_nf = $ref->ref_num_nf;
                $referenciado->ref_serie = $ref->ref_serie;
                $referenciados[] = $referenciado;
            }
        }

        $formas = NfeDuplicata::where("nfe_id", $nfe->id)->select(NfeDuplicata::raw('DISTINCT tPag, SUM(vDup) AS soma'))->groupBy('tPag')->get();
        $pagamentos = array();
        if (count($formas) > 0) {
            foreach ($formas as $f) {
                $pagamento = new \stdClass();
                $pagamento->tPag = $f->tPag;
                $pagamento->vPag = $f->soma;
                $pagamento->CNPJ = NULL;
                $pagamento->tBand = NULL;
                $pagamento->cAut = NULL;
                $pagamento->tpIntegra = NULL; //incluso na NT 2015/002
                $pagamento->indPag = NULL; //0= Pagamento à Vista 1= Pagamento à Prazo
                $pagamentos[] = $pagamento;
            }
        }

        $nota->vTroco = $nfe->vTroco; //incluso no layout 4.00, obrigatório informar para NFCe (65)


        $dados = array(
            "nota" => $nota,
            "emitente" => $emitente,
            "destinatario" => $destinatario,
            "itens" => $itens,
            "cobranca" => ($cobranca) ?? null,
            "pagamentos" => ($pagamentos) ?? null,
            "autorizados" => ($autorizados) ?? null,
            "referenciados" => ($referenciados) ?? null,
        );

        return $dados;

    }

    public static function prepararNfce($nfce)
    {
        $nota = new \stdClass();
        $nota->id = $nfce->id;
        $nota->venda_id = $nfce->venda_id;
        $nota->empresa_id = $nfce->empresa_id;
        $nota->status_id = $nfce->status_id;
        $nota->chave = $nfce->chave;
        $nota->recibo = $nfce->recibo;
        $nota->protocolo = $nfce->protocolo;

        $nota->cUF = $nfce->cUF;
        $nota->natOp = $nfce->natOp;
        $nota->serie = $nfce->serie;
        $nota->nNF = $nfce->nNF;
        $nota->tpNF = $nfce->tpNF;
        $nota->idDest = $nfce->idDest;
        $nota->tpImp = $nfce->tpImp;
        $nota->tpEmis = $nfce->tpEmis;
        $nota->tpAmb = $nfce->tpAmb;
        $nota->finNFe = $nfce->finNFe;
        $nota->indFinal = $nfce->indFinal;
        $nota->indPres = $nfce->indPres;
        $nota->procEmi = $nfce->procEmi;
        $nota->verProc = $nfce->verProc;
        $nota->modFrete = $nfce->modFrete;

        //Intermediário
        $nota->indIntermed = $nfce->indIntermed;
        $nota->cnpjIntermed = $nfce->cnpjIntermed;
        $nota->idCadIntTran = $nfce->idCadIntTran;

        //Responsável técnico
        $nota->CNPJ = $nfce->resp_CNPJ; //CNPJ da pessoa jurídica responsável pelo sistema utilizado na emissão do documento fiscal eletrônico
        $nota->xContato = $nfce->resp_xContato; //Nome da pessoa a ser contatada
        $nota->email = $nfce->resp_email; //E-mail da pessoa jurídica a ser contatada
        $nota->fone = $nfce->resp_fone; //Telefone da pessoa jurídica/física a ser contatada
        $nota->CSRT = $nfce->resp_CSRT; //Código de Segurança do Responsável Técnico
        $nota->idCSRT = $nfce->resp_idCSRT; //Identificador do CSRT

        //Destinatario
        $nota->cliente_cpf = $nfce->cliente_cpf;
        $nota->cliente_nome = $nfce->cliente_nome;
        $nota->cliente_cnpj = $nfce->cliente_cnpj;

        //Emitente
        $emitente = new \stdClass();
        $emitente->xNome = $nfce->em_xNome;
        $emitente->xFant = $nfce->em_xFant;
        $emitente->IE = $nfce->em_IE;
        $emitente->IEST = $nfce->em_IEST;
        $emitente->IM = $nfce->em_IM;
        $emitente->CNAE = $nfce->em_CNAE;
        $emitente->CRT = $nfce->em_CRT;
        $emitente->CNPJ = $nfce->em_CNPJ; //indicar apenas um CNPJ ou CPF
        $emitente->CPF = $nfce->em_CPF;
        $emitente->xLgr = $nfce->em_xLgr;
        $emitente->nro = $nfce->em_nro;
        $emitente->xCpl = $nfce->em_xCpl;
        $emitente->xBairro = $nfce->em_xBairro;
        $emitente->cMun = $nfce->em_cMun;
        $emitente->xMun = $nfce->em_xMun;
        $emitente->UF = $nfce->em_UF;
        $emitente->CEP = $nfce->em_CEP;
        $emitente->cPais = $nfce->em_cPais;
        $emitente->xPais = $nfce->em_xPais;
        $emitente->fone = $nfce->em_fone;

        $itens = array();
        if (count($nfce->itens) > 0) {
            foreach ($nfce->itens as $item) {
                $produto = new \stdClass();

                $produto->item = $item->numero_item;
                $produto->cProd = $item->cProd;
                $produto->cEAN = $item->cEAN;
                $produto->xProd = $item->xProd;
                $produto->NCM = $item->NCM;
                $produto->cBenef = $item->cBenef;
                $produto->EXTIPI = $item->EXTIPI;
                $produto->CFOP = $item->CFOP;
                $produto->CEST = $item->CEST;

                $produto->uCom = $item->uCom;
                $produto->qCom = $item->qCom;
                $produto->vUnCom = $item->vUnCom;
                $produto->vProd = $item->vProd;
                $produto->cEANTrib = $item->cEANTrib;
                $produto->uTrib = $item->uTrib;
                $produto->qTrib = $item->qTrib;
                $produto->vUnTrib = $item->vUnTrib;
                $produto->vFrete = $item->vFrete;
                $produto->vSeg = $item->vSeg;
                $produto->vDesc = $item->vDesc;
                $produto->vOutro = $item->vOutro;
                $produto->indTot = $item->indTot;
                $produto->xPed = $item->xPed;
                $produto->nItemPed = $item->nItemPed;
                $produto->nFCI = $item->nFCI;

                $observacao = new \stdClass();
                //$observacao->item =1; //item da NFe
                //$observacao->infAdProd = 'informacao adicional do item';

                $imposto = new \stdClass();
                //$imposto->item       = 1; //item da NFe
                // $imposto->vTotTrib   = null;

                $icms = new \stdClass();
                $icms->item = $item->numero_item; //item da NFe
                $icms->orig = $item->orig;
                if (intVal($item->cstICMS) > 100) {
                    $icms->CSOSN = $item->cstICMS;
                    unset($icms->CST);
                } else {
                    $icms->CST = $item->cstICMS;
                }

                $icms->pCredSN = $item->pCredSN;
                $icms->vCredICMSSN = $item->vCredICMSSN;
                $icms->modBCST = $item->modBCST;
                $icms->pMVAST = $item->pMVAST;
                $icms->pRedBCST = $item->pRedBCST;
                $icms->vBCST = $item->vBCST;
                $icms->pICMSST = $item->pICMSST;
                $icms->vICMSST = $item->vICMSST;
                $icms->vBCFCPST = $item->vBCFCPST; //incluso no layout 4.00
                $icms->pFCPST = $item->pFCPST; //incluso no layout 4.00
                $icms->vFCPST = $item->vFCPST; //incluso no layout 4.00
                $icms->vBCSTRet = $item->vBCSTRet;
                $icms->pST = $item->pST;
                $icms->vICMSSTRet = $item->vICMSSTRet;
                $icms->vBCFCPSTRet = $item->vBCFCPSTRet; //incluso no layout 4.00
                $icms->pFCPSTRet = $item->pFCPSTRet; //incluso no layout 4.00
                $icms->vFCPSTRet = $item->vFCPSTRet; //incluso no layout 4.00
                $icms->modBC = $item->modBC;
                $icms->vBC = $item->vBCICMS;
                $icms->pRedBC = $item->pRedBC;
                $icms->pICMS = $item->pICMS;
                $icms->vICMS = $item->vICMS;
                $icms->pRedBCEfet = $item->pRedBCEfet;
                $icms->vBCEfet = $item->vBCEfet;
                $icms->pICMSEfet = $item->pICMSEfet;
                $icms->vICMSEfet = $item->vICMSEfet;
                $icms->vICMSSubstituto = $item->vICMSSubstituto;


                $icms->modBC = $item->modBC;
                $icms->vBC = $item->vBCICMS;
                $icms->pICMS = $item->pICMS;
                $icms->vICMS = $item->vICMS;
                $icms->pFCP = $item->pFCP;
                $icms->vFCP = $item->vFCP;
                $icms->vBCFCP = $item->vBCFCP;
                $icms->modBCST = $item->modBCST;
                $icms->pMVAST = $item->pMVAST;
                $icms->pRedBCST = $item->pRedBCST;
                $icms->vBCST = $item->vBCST;
                $icms->pICMSST = $item->pICMSST;
                $icms->vICMSST = $item->vICMSST;
                $icms->vBCFCPST = $item->vBCFCPST;
                $icms->pFCPST = $item->pFCPST;
                $icms->vFCPST = $item->vFCPST;
                $icms->vICMSDeson = $item->vICMSDeson;
                $icms->motDesICMS = $item->motDesICMS;
                $icms->pRedBC = $item->pRedBC;
                $icms->vICMSOp = $item->vICMSOp;
                $icms->pDif = $item->pDif;
                $icms->vICMSDif = $item->vICMSDif;
                $icms->vBCSTRet = $item->vBCSTRet;
                $icms->pST = $item->pST;
                $icms->vICMSSTRet = $item->vICMSSTRet;
                $icms->vBCFCPSTRet = $item->vBCFCPSTRet;
                $icms->pFCPSTRet = $item->pFCPSTRet;
                $icms->vFCPSTRet = $item->vFCPSTRet;
                $icms->pRedBCEfet = $item->pRedBCEfet;
                $icms->vBCEfet = $item->vBCEfet;
                $icms->pICMSEfet = $item->pICMSEfet;
                $icms->vICMSEfet = $item->vICMSEfet;
                $icms->vICMSSubstituto = $item->vICMSSubstituto; //NT 2020.005 v1.20
                $icms->vICMSSTDeson = $item->vICMSSTDeson; //NT 2020.005 v1.20
                $icms->motDesICMSST = $item->motDesICMSST; //NT 2020.005 v1.20
                $icms->pFCPDif = $item->pFCPDif; //NT 2020.005 v1.20
                $icms->vFCPDif = $item->vFCPDif; //NT 2020.005 v1.20
                $icms->vFCPEfet = $item->vFCPEfet; //NT 2020.005 v1.20

                $ipi = new \stdClass();
                $ipi->item = $item->numero_item; //item da NFe
                $ipi->clEnq = $item->clEnq;
                $ipi->CNPJProd = $item->CNPJProd;
                $ipi->cSelo = $item->cSelo;
                $ipi->qSelo = $item->qSelo;
                $ipi->cEnq = $item->cEnq;
                $ipi->CST = $item->cstIPI;
                $ipi->vIPI = $item->vIPI;
                $ipi->vBC = $item->vBCIPI;
                $ipi->pIPI = $item->pIPI;
                $ipi->qUnid = $item->qUnidIPI;
                $ipi->vUnid = $item->vUnidIPI;

                $pis = new \stdClass();
                $pis->item = $item->numero_item; //item da NFe
                $pis->CST = $item->cstPIS;
                $pis->vBC = $item->vBCPIS;
                $pis->pPIS = $item->pPIS;
                $pis->vPIS = $item->vPIS;
                $pis->qBCProd = $item->qBCProdPis;
                $pis->vAliqProd = $item->vAliqProd_pis;

                $cofins = new \stdClass();
                $cofins->item = $item->numero_item; //item da NFe
                $cofins->CST = $item->cstCOFINS;
                $cofins->vBC = $item->vBCCOFINS;
                $cofins->pCOFINS = $item->pCOFINS;
                $cofins->vCOFINS = $item->vCOFINS;
                $cofins->qBCProd = $item->qBCProdConfis;
                $cofins->vAliqProd = $item->vAliqProd_cofins;

                $itens[] = array(
                    "produto" => $produto,
                    "observacao" => $observacao,
                    "imposto" => $imposto,
                    "icms" => ($icms) ?? null,
                    "ipi" => $ipi,
                    "pis" => $pis,
                    "cofins" => $cofins
                );

            }
        }


        $nota->vBC = $nfce->vBC;
        $nota->vICMS = $nfce->vICMS;
        $nota->vICMSDeson = $nfce->vICMSDeson;
        $nota->vFCP = $nfce->vFCP; //incluso no layout 4.00
        $nota->vBCST = $nfce->vBCST;
        $nota->vST = $nfce->vST;
        $nota->vFCPST = $nfce->vFCPST; //incluso no layout 4.00
        $nota->vFCPSTRet = $nfce->vFCPSTRet; //incluso no layout 4.00
        $nota->vProd = $nfce->vProd;
        $nota->vFrete = $nfce->vFrete;
        $nota->vSeg = $nfce->vSeg;
        $nota->vDesc = $nfce->vDesc;
        $nota->vII = $nfce->vII;
        $nota->vIPI = $nfce->vIPI;
        $nota->vIPIDevol = $nfce->vIPIDevol; //incluso no layout 4.00
        $nota->vPIS = $nfce->vPIS;
        $nota->vCOFINS = $nfce->vCOFINS;
        $nota->vOutro = $nfce->vOutro;
        $nota->vNF = $nfce->vNF;
        $nota->vTotTrib = $nfce->vTotTrib;

        $nota->infAdFisco = $nfce->infAdFisco;
        $nota->infCpl = $nfce->infCpl;


        $duplicatas = array();
        if ($nfce->duplicatas) {
            foreach ($nfce->duplicatas as $dup) {
                $duplicata = new \stdClass();
                $duplicata->nDup = $dup->nDup;
                $duplicata->dVenc = $dup->dVenc;
                $duplicata->vDup = $dup->vDup;
                $duplicatas[] = $duplicata;
            }
        }

        $cobranca = new \stdClass();
        $cobranca->nFat = $nfce->nFat;
        $cobranca->vOrig = $nfce->vOrig;
        $cobranca->vDesc = $nfce->vDesc;
        $cobranca->vLiq = $nfce->vLiq;
        $cobranca->duplicatas = $duplicatas;


        $formas = NfceDuplicata::where("nfce_id", $nfce->id)->select(NfceDuplicata::raw('DISTINCT tPag, SUM(vDup) AS vDup'))->groupBy('tPag')->get();

        $pagamentos = array();
        if (count($formas) > 0) {
            foreach ($formas as $f) {
                $pagamento = new \stdClass();
                $pagamento->tPag = $f->tPag;
                $pagamento->vPag = $f->vDup;
                $pagamento->CNPJ = NULL;
                $pagamento->tBand = NULL;
                $pagamento->cAut = NULL;
                $pagamento->tpIntegra = NULL; //incluso na NT 2015/002
                $pagamento->indPag = NULL; //0= Pagamento à Vista 1= Pagamento à Prazo
                $pagamentos[] = $pagamento;
            }
        }

        $nota->vTroco = $nfce->vTroco; //incluso no layout 4.00, obrigatório informar para NFCe (65)


        $dados = (object)array(
            "nota" => $nota,
            "emitente" => $emitente,
            "itens" => $itens,
            "cobranca" => ($cobranca) ?? null,
            "pagamentos" => ($pagamentos) ?? null,
        );


        return $dados;

    }
}

