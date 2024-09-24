<?php

namespace App\Services;

use App\Models\Emitente;
use App\Models\Nfce;
use App\Models\Nfe;
use Illuminate\Support\Facades\Storage;
use NFePHP\Common\Certificate;
use NFePHP\DA\NFe\Daevento;
use NFePHP\DA\NFe\Danfe;
use NFePHP\NFe\Complements;
use NFePHP\NFe\Make;
use NFePHP\NFe\Tools;
use NFePHP\NFe\Common\Standardize;
use App\Models\Venda;
use App\Models\NfeXml;


class NfeService
{
    private static $configJson;
    private static $certificado_digital;
    private static $tools;
    private static $pastaAmbiente;
    private static $pastaEmpresa;


    public static function config($notafiscal)
    {
        $emitente = Emitente::where(["cnpj" => $notafiscal->emitente->CNPJ, "empresa_id" => $notafiscal->nota->empresa_id])->first();

        $arr = [
            "atualizacao" => date('Y-m-d h:i:s'),
            "tpAmb" => intVal($notafiscal->nota->tpAmb),
            "razaosocial" => $emitente->razao_social,
            "cnpj" => $emitente->cnpj,
            "siglaUF" => $emitente->uf,
            "schemes" => "PL_009_V4",
            "versao" => '4.00',
            "tokenIBPT" => "",
            "CSC" => "",
            "CSCid" => "",
            "proxyConf" => [
                "proxyIp" => "",
                "proxyPort" => "",
                "proxyUser" => "",
                "proxyPass" => ""
            ]
        ];

        self::$configJson = json_encode($arr);
        self::$certificado_digital = $emitente->certificado->certificado_arquivo_binario;
        self::$tools = new Tools(self::$configJson, Certificate::readPfx(self::$certificado_digital, $emitente->certificado->certificado_senha));
        self::$pastaAmbiente = ($notafiscal->nota->tpAmb == "1") ? "producao" : "homologacao";
        self::$pastaEmpresa = $emitente->empresa->pasta;
        //$tools->disableCertValidation(true); //tem que desabilitar
        self::$tools->model('55');
    }

    public static function gerarNfe($notafiscal)
    {
        try {
            self::config($notafiscal);
        } catch (\Exception $e) {
            $retorno = new \stdClass();
            $retorno->tem_erro = true;
            $retorno->titulo = "Certificado Digital";
            $retorno->erro = $e->getMessage();
            return $retorno;
            exit;
        }

        $nfe = new Make();
        NfeTagService::infNfe($nfe, $notafiscal->nota->chave);
        NfeTagService::identificacao($nfe, $notafiscal);

        if ($notafiscal->nota->finNFe > 1) {

            if (isset($notafiscal->referenciados)) {
                NfeTagService::docReferenciado($nfe, $notafiscal->referenciados);
            }
        }
        NfeTagService::emitente($nfe, $notafiscal->emitente);
        NfeTagService::destinatario($nfe, $notafiscal->destinatario);

        $cont = 1;
        foreach ($notafiscal->itens as $item) {
            NfeTagService::dadosProduto($cont, $nfe, (object)$item["produto"]);
            NfeTagService::imposto($cont, $nfe, (object)$item["produto"]);

            NfeTagService::icms($cont, $nfe, (object)$item["icms"]);

            if ($item["ipi"]->vIPI)
                NfeTagService::ipi($cont, $nfe, (object)$item["ipi"]);

            NfeTagService::pis($cont, $nfe, (object)$item["pis"]);
            NfeTagService::cofins($cont, $nfe, (object)$item["cofins"]);

            if (isset($item["observacao"]->infAdProd))
                NfeTagService::infAdProduto($cont, $nfe, (object)$item["observacao"]);
            $cont++;
        }

        NfeTagService::totais($nfe, $notafiscal->nota);
        NfeTagService::transp($nfe, $notafiscal->nota);
        if ($notafiscal->nota->transp_xNome)
            NfeTagService::transportadora($nfe, $notafiscal->nota);

        if ($notafiscal->nota->transp_veic_placa || $notafiscal->nota->transp_veic_placa)
            NfeTagService::trator($nfe, $notafiscal->nota);

        if ($notafiscal->nota->transp_reboque_placa || $notafiscal->nota->transp_reboque_UF)
            NfeTagService::reboque($nfe, $notafiscal->nota);

        if (count($notafiscal->cobranca->duplicatas) > 0) {
            if ($notafiscal->nota->finNFe != 3 && $notafiscal->nota->finNFe != 4) {
                NfeTagService::cobranca($nfe, $notafiscal->cobranca);
            }
        }

        $std = new \stdClass();
        $std->vTroco = $notafiscal->nota->vTroco;
        $nfe->tagpag($std);

        if (count($notafiscal->pagamentos) > 0) {
            if ($notafiscal->nota->finNFe == 3 || $notafiscal->nota->finNFe == 4) {
                $std = new \stdClass();
                $std->tPag = 90;
                $std->vPag = 0.00;
                $nfe->tagdetPag($std);
            } else {
                NfeTagService::pagamentos($nfe, $notafiscal->pagamentos);
            }

        } else {
            $std = new \stdClass();
            $std->indPag = '0';
            $std->tPag = '01';
            $std->vPag = $notafiscal->nota->vNF;
            $nfe->tagdetPag($std);
        }

        NfeTagService::infRespTec($nfe, $notafiscal->nota);
        // NfeTagService::intermed($nfe, $notafiscal->nota);

        if (($notafiscal->nota->infAdFisco) || ($notafiscal->nota->infCpl))
            NfeTagService::infAdic($nfe, $notafiscal->nota);

        if (isset($notafiscal->autorizados)) {
            NfeTagService::autorizados($nfe, $notafiscal->autorizados);
        }
        return self::gerarXml($nfe, $notafiscal);

    }

    public static function gerarXml($nfe, $notafiscal)
    {
        $retorno = new \stdClass();
        try {
            $resultado = $nfe->montaNFe();
            if ($resultado) {
                $xml = $nfe->getXML();
                $chave = $nfe->getChave();
                Storage::disk('local')->put('venda_teste.xml', $xml);

                $path = "storage/" . self::$pastaEmpresa . "/xml/nfe/" . self::$pastaAmbiente . "/temporarias/";
                $nome_arquivo = $chave . "-nfe.xml";

                if (!file_exists($path)) {
                    mkdir($path, 07777, true);
                }

                file_put_contents($path . $nome_arquivo, $xml);
                chmod($path, 07777);
                Nfe::where("id", $notafiscal->nota->id)->update(["chave" => $chave]);
                $tem = NfeXml::where("nfe_id", $notafiscal->nota->id)->first();
                if (!$tem) {
                    NfeXml::Create(["nfe_id" => $notafiscal->nota->id, "xml" => $nfe->getXML(), "chave" => $chave, "empresa_id" => $notafiscal->nota->empresa_id]);
                } else {
                    $tem->update(["xml" => $nfe->getXML(), "chave" => $chave]);
                }
                $retorno->tem_erro = false;
                $retorno->titulo = "arquivo XML gerado com Sucesso";
                $retorno->erro = "";
                $retorno->chave = $chave;
                $retorno->xml = $xml;

            } else {
                Nfe::where("id", $notafiscal->nota->id)->update(["status_id" => config('constantes.status.ERRO_AO_GERAR_XML')]);
                $retorno->tem_erro = true;
                $retorno->titulo = "Não foi possível gerar o XML";
                $retorno->erro = $nfe->getErrors();
            }


        } catch (\Exception $e) {
            Nfe::where("id", $notafiscal->nota->id)->update(["status_id" => config('constantes.status.ERRO_AO_GERAR_XML')]);
            $retorno->tem_erro = true;
            $retorno->titulo = "Não foi possível gerar o arquivo XML";
            if ($nfe->getErrors() != null)
                $retorno->erro = $nfe->getErrors();
            else
                $retorno->erro = $e->getMessage();
        }
        return $retorno;
    }


    public static function assinarXml($xml, $chave, $notafiscal)
    {
        self::config($notafiscal);
        $retorno = new \stdClass();
        try {
            $response = self::$tools->signNFe($xml);


            $path = "storage/" . self::$pastaEmpresa . "/xml/nfe/" . self::$pastaAmbiente . "/assinadas/";
            $nome_arquivo = $chave . "-nfe.xml";

            if (!file_exists($path)) {
                mkdir($path, 07777, true);
            }

            file_put_contents($path . $nome_arquivo, $response);
            chmod($path, 07777);

            $retorno->tem_erro = false;
            $retorno->titulo = "XML assinado com sucesso";
            $retorno->erro = "";
            $retorno->xml = $response;

        } catch (\Exception $e) {
            Nfe::where("id", $notafiscal->nota->id)->update(["status_id" => config('constantes.status.ERRO_AO_ASSINAR_XML')]);
            $retorno->tem_erro = true;
            $retorno->titulo = "Erro ao assinar o XML";
            $retorno->erro = $e->getMessage();
        }

        return $retorno;
    }

    public static function enviarXML($xml, $chave, $notafiscal)
    {
        self::config($notafiscal);
        $retorno = new \stdClass();
        try {

            $idLote = str_pad($notafiscal->nota->nNF, 15, '0', STR_PAD_LEFT);
            //envia o xml para pedir autorização ao SEFAZ
            $resp = self::$tools->sefazEnviaLote([$xml], $idLote);
            sleep(2);
            //transforma o xml de retorno em um stdClass
            $st = new Standardize();
            $std = $st->toStd($resp);
            if ($std->cStat != 103) {
                Nfe::where("id", $notafiscal->nota->id)->update(["status_id" => config('constantes.status.ERRO_AO_ENVIAR_NFE')]);
                $retorno->tem_erro = true;
                $retorno->titulo = "Não foi possível enviar o XML para a Sefaz";
                $retorno->erro = "[$std->cStat] $std->xMotivo";
                return $retorno;
            }
            $recibo = ($std->infRec->nRec) ?? NULL;
            Nfe::where("id", $notafiscal->nota->id)->update(["recibo" => $recibo]);
            $retorno->tem_erro = false;
            $retorno->titulo = "XML enviado com sucesso";
            $retorno->erro = "";
            $retorno->recibo = $std->infRec->nRec;

        } catch (\Exception $e) {
            Nfe::where("id", $notafiscal->nota->id)->update(["status_id" => config('constantes.status.ERRO_AO_ENVIAR_NFE')]);
            $retorno->tem_erro = true;
            $retorno->titulo = "Erro ao enviar o lote para a Sefaz";
            $retorno->erro = $e->getMessage();
        }
        return $retorno;
    }


    public static function consultarProcessamento($notafiscal)
    {
        self::config($notafiscal);
        $path_assinado = "storage/" . self::$pastaEmpresa . "/xml/nfe/" . self::$pastaAmbiente . "/assinadas/" . $notafiscal->nota->chave . "-nfe.xml";
        $xml_assinado = file_get_contents($path_assinado);
        return self::consultarPorRecibo($xml_assinado, $notafiscal->nota->chave, $notafiscal->nota->recibo, $notafiscal);
    }

    public static function consultarPorRecibo($xml, $chave, $recibo, $notafiscal)
    {
        self::config($notafiscal);
        $retorno = new \stdClass();
        try {
            //consulta número de recibo
            //$numeroRecibo = número do recíbo do envio do lote
            $xmlResp = self::$tools->sefazConsultaRecibo($recibo, $notafiscal->nota->tpAmb);

            //transforma o xml de retorno em um stdClass
            $st = new Standardize();
            $std = $st->toStd($xmlResp);

            if ($std->cStat == '103') { //lote enviado
                Nfe::where("id", $notafiscal->nota->id)->update(["status_id" => config('constantes.status.EM_PROCESSAMENTO')]);
                $retorno->tem_erro = true;
                $retorno->titulo = "Protocolo ainda não disponível";
                $retorno->erro = "O lote ainda não foi processado";
                $retorno->status_id = config('constantes.status.EM_PROCESSAMENTO');

                return $retorno;
            }
            if ($std->cStat == '105') { //lote em processamento
                Nfe::where("id", $notafiscal->nota->id)->update(["status_id" => config('constantes.status.EM_PROCESSAMENTO')]);
                $retorno->tem_erro = true;
                $retorno->titulo = "Protocolo sendo processado";
                $retorno->erro = "Lote em processamento, tente, mais tarde";
                $retorno->status_id = config('constantes.status.EM_PROCESSAMENTO');

                return $retorno;
            }

            if ($std->cStat == '104') { //lote processado (tudo ok)
                if ($std->protNFe->infProt->cStat == '100') { //Autorizado o uso da NF-e
                    $protocolo = $std->protNFe->infProt->nProt;
                    $xml_autorizado = Complements::toAuthorize($xml, $xmlResp);

                    $path = "storage/" . self::$pastaEmpresa . "/xml/nfe/" . self::$pastaAmbiente . "/autorizadas/";
                    $nome_arquivo = $chave . "-nfe.xml";

                    if (!file_exists($path)) {
                        mkdir($path, 07777, true);
                    }

                    file_put_contents($path . $nome_arquivo, $xml_autorizado);
                    chmod($path, 07777);

                    Nfe::where("id", $notafiscal->nota->id)->update(["protocolo" => $protocolo, "status_id" => config('constantes.status.AUTORIZADO')]);
                    Venda::where("id", $notafiscal->nota->venda_id)->update(["chave" => $chave, "nfe_id" => $notafiscal->nota->id]);
                    $retorno->tem_erro = false;
                    $retorno->titulo = "XML autorizado com sucesso";
                    $retorno->erro = "";
                    $retorno->recibo = $recibo;
                    $retorno->chave = $chave;
                    $retorno->status_id = config('constantes.status.AUTORIZADO');
                    $retorno->protocolo = $protocolo;
                    $retorno->xml = $xmlResp;

                } elseif (in_array($std->protNFe->infProt->cStat, ["110", "301", "302"])) { //DENEGADAS
                    Nfe::where("id", $notafiscal->nota->id)->update(["status_id" => config('constantes.status.DENEGADA')]);
                    $retorno->tem_erro = true;
                    $retorno->titulo = "Nota Denegada";
                    $retorno->erro = $std->protNFe->infProt->cStat . ":" . $std->protNFe->infProt->xMotivo;
                    $retorno->protocolo = $std->protNFe->infProt->nProt;
                    $retorno->xml = $xmlResp;
                    $retorno->status_id = config('constantes.status.DENEGADA');
                    $retorno->cstat = $std->protNFe->infProt->cStat;
                    return $retorno;
                } else { //não autorizada (rejeição)
                    Nfe::where("id", $notafiscal->nota->id)->update(["status_id" => config('constantes.status.REJEITADO')]);
                    $retorno->tem_erro = true;
                    $retorno->titulo = "Nota Rejeitada";
                    $retorno->erro = $std->protNFe->infProt->cStat . ":" . $std->protNFe->infProt->xMotivo;
                    $retorno->cstat = $std->protNFe->infProt->cStat;
                    $retorno->status_id = config('constantes.status.REJEITADO');
                    return $retorno;

                }
            } else { //outros erros possíveis
                Nfe::where("id", $notafiscal->nota->id)->update(["status_id" => config('constantes.status.REJEITADO')]);
                $retorno->tem_erro = true;
                $retorno->titulo = "Nota Rejeitada";
                $retorno->erro = $std->cStat . ":" . $std->xMotivo;
                $retorno->cstat = $std->cStat;
                $retorno->status_id = config('constantes.status.REJEITADO');
                return $retorno;
            }

        } catch (\Exception $e) {
            Nfe::where("id", $notafiscal->nota->id)->update(["status_id" => config('constantes.status.REJEITADO')]);
            $retorno->tem_erro = true;
            $retorno->titulo = "Erro ao consultar a nota na Sefaz";
            $retorno->erro = $e->getMessage();
            $retorno->status_id = config('constantes.status.REJEITADO');
            return $retorno;
        }
        return $retorno;
    }

    public static function cce($id_nfe)
    {
        $nfe = Nfe::where("id", $id_nfe)->first();
        $pastaAmbiente = ($nfe->tpAmb == "1") ? "producao" : "homologacao";
        $path = "storage/" . $nfe->empresa->pasta . "/xml/nfe/" . $pastaAmbiente . "/correcao/" . $nfe->chave . "-nfe.xml";
        $xml = file_get_contents($path);
        $emitente = new \stdClass();
        $emitente->razao = $nfe->em_xNome;
        $emitente->logradouro = $nfe->em_xLgr;
        $emitente->numero = $nfe->em_nro;
        $emitente->complemento = $nfe->em_xCpl;
        $emitente->bairro = $nfe->em_xBairro;
        $emitente->CEP = $nfe->em_CEP;
        $emitente->municipio = $nfe->em_xMun;
        $emitente->UF = $nfe->em_UF;
        $emitente->telefone = $nfe->em_fone;
        $emitente->email = "";

        $retorno = new \stdClass();
        try {
            $daevento = new Daevento($xml, objToArray($emitente));
            $daevento->debugMode(true);
            $daevento->creditsIntegratorFooter('WEBNFe Sistemas - http://www.webenf.com.br');
            $pdf = $daevento->render();
            $retorno->tem_erro = false;
            $retorno->titulo = "Pdf gerado com sucesso";
            $retorno->erro = "";
            $retorno->pdf = $pdf;
            return $retorno;
        } catch (\Exception $e) {
            $retorno->tem_erro = true;
            $retorno->titulo = "Erro gerar o PDF";
            $retorno->erro = $e->getMessage();
            $retorno->pdf = NULL;
            return $retorno;
        }

        return $retorno;
    }

    public static function imprimirCancelado($id_nfe)
    {
        $nfe = Nfe::find($id_nfe);
        dd($nfe);
        $pastaAmbiente = ($nfe->tpAmb == "1") ? "producao" : "homologacao";
        $path = "storage/" . $nfe->empresa->pasta . "/xml/nfe/" . $pastaAmbiente . "/cancelado/" . $nfe->chave . "-nfe.xml";
        $xml = file_get_contents($path);
        $retorno = new \stdClass();
        try {
            $emitente = new \stdClass();
            $emitente->razao = $nfe->em_xNome;
            $emitente->logradouro = $nfe->em_xLgr;
            $emitente->numero = $nfe->em_nro;
            $emitente->complemento = $nfe->em_xCpl;
            $emitente->bairro = $nfe->em_xBairro;
            $emitente->CEP = $nfe->em_CEP;
            $emitente->municipio = $nfe->em_xMun;
            $emitente->UF = $nfe->em_UF;
            $emitente->telefone = $nfe->em_fone;
            $emitente->email = "";


            $daevento = new Daevento($xml, objToArray($emitente));
            $daevento->debugMode(true);
            $pdf = $daevento->render();
            $retorno->tem_erro = false;
            $retorno->titulo = "Pdf gerado com sucesso";
            $retorno->erro = "";
            $retorno->chave = $nfe->chave;
            $retorno->pdf = $pdf;
            return $retorno;
        } catch (\Exception $e) {
            $retorno->tem_erro = true;
            $retorno->titulo = "Erro gerar o PDF";
            $retorno->erro = $e->getMessage();
            return $retorno;
        }

        return $retorno;
    }


    public static function danfe($chave)
    {

        $nfe = Nfe::where("chave", $chave)->first();
        $pastaAmbiente = ($nfe->tpAmb == "1") ? "producao" : "homologacao";
        $path = "storage/" . $nfe->empresa->pasta . "/xml/nfe/" . $pastaAmbiente . "/autorizadas/" . $chave . "-nfe.xml";
        $xml = file_get_contents($path);

        $retorno = new \stdClass();
        try {
            $danfe = new Danfe($xml);
            $pdf = $danfe->render();

            $retorno->tem_erro = false;
            $retorno->titulo = "Pdf gerado com sucesso";
            $retorno->erro = "";
            $retorno->pdf = $pdf;
            return $retorno;
        } catch (\Exception $e) {
            $retorno->tem_erro = true;
            $retorno->titulo = "Erro gerar o PDF";
            $retorno->erro = $e->getMessage();
            $retorno->pdf = NULL;
            return $retorno;
        }
        return $retorno;

    }

    public static function simulaDanfe($xml)
    {
        $retorno = new \stdClass();
        try {
            $danfe = new Danfe($xml);
            $pdf = $danfe->render();
            $retorno->tem_erro = false;
            $retorno->titulo = "Pdf gerado com sucesso";
            $retorno->erro = "";
            $retorno->pdf = $pdf;
        } catch (\Exception $e) {
            $retorno->tem_erro = true;
            $retorno->titulo = "Erro gerar o PDF";
            $retorno->erro = $e->getMessage();
        }

        return $retorno;
    }

    public static function cartaCorrecao($xCorrecao, $notafiscal)
    {
        self::config($notafiscal);
        $retorno = new \stdClass();
        try {
            $nSeqEvento = $notafiscal->nota->sequencia_cce + 1;
            $path = "storage/" . self::$pastaEmpresa . "/xml/nfe/" . self::$pastaAmbiente . "/correcao/";
            $nome_arquivo = $notafiscal->nota->chave . "-nfe.xml";
            if (!file_exists($path)) {
                mkdir($path, 07777, true);
            }

            $response = self::$tools->sefazCCe($notafiscal->nota->chave, $xCorrecao, $nSeqEvento);
            sleep(1);
            $stdCl = new Standardize($response);

            $std = $stdCl->toStd();
            $arr = $stdCl->toArray();
            $json = $stdCl->toJson();
            sleep(1);
            if ($std->cStat != 128) {
                $retorno->tem_erro = true;
                $retorno->titulo = "Erro gerar o Carta de Correção";
                $retorno->erro = $std->xMotivo;
                $retorno->retorno = NULL;
                return $retorno;
            } else {
                $cStat = $std->retEvento->infEvento->cStat;
                if ($cStat == '135' || $cStat == '136') {

                    $xml = Complements::toAuthorize(self::$tools->lastRequest, $response);
                    file_put_contents($path . $nome_arquivo, $xml);
                    chmod($path, 07777);
                    Nfe::where("id", $notafiscal->nota->id)->update(["sequencia_cce" => $nSeqEvento]);
                    $retorno->tem_erro = false;
                    $retorno->titulo = "Carta de Correção gerada com sucesso";
                    $retorno->erro = "";
                    $retorno->retorno = $json;
                    return $retorno;
                } else {
                    $retorno->tem_erro = true;
                    $retorno->titulo = "Erro gerar o Carta de Correção";
                    $retorno->erro = $json;
                    $retorno->retorno = NULL;
                    return $retorno;
                }
            }
        } catch (\Exception $e) {
            $retorno->tem_erro = true;
            $retorno->titulo = "Erro gerar o Carta de Correção";
            $retorno->erro = $e->getMessage();
            $retorno->retorno = NULL;
            return $retorno;
        }
        return $retorno;
    }

    public static function inutilizar($dados, $notafiscal)
    {
        self::config($notafiscal);
        $retorno = new \stdClass();
        try {
            $nSerie = $dados->nSerie;
            $nIni = $dados->nIni;
            $nFin = $dados->nFin;
            $xJust = $dados->justificativa;
            $response = self::$tools->sefazInutiliza($nSerie, $nIni, $nFin, $xJust);

            $stdCl = new Standardize($response);
            $std = $stdCl->toStd();
            $protocolo = $std->infInut->nProt ?? null;
            if ($protocolo) {
                Nfe::where("id", $dados->id)->update(["protocolo" => $protocolo, "status_id" => config("constantes.status.INUTILIZADO")]);
                $retorno->tem_erro = false;
                $retorno->titulo = "Nota Inutilizada com sucesso";
                $retorno->erro = "";
                $retorno->resultado = $std;
                return $retorno;
            } else {
                $retorno->tem_erro = true;
                $retorno->titulo = "Erro ao inutilizar a nota";
                $retorno->erro = $std->infInut->xMotivo ?? null;
                return $retorno;
            }

        } catch (\Exception $e) {
            $retorno->tem_erro = true;
            $retorno->titulo = "Erro ao inutilizar a nota";
            $retorno->erro = $e->getMessage();
        }

        return $retorno;
    }

    public static function cancelarPorChave($justificativa, $notafiscal)
    {
        self::config($notafiscal);
        $retorno = new \stdClass();
        try {
            $chave = $notafiscal->nota->chave;
            $response = self::$tools->sefazConsultaChave($chave);
            $path = "storage/" . self::$pastaEmpresa . "/xml/nfe/" . self::$pastaAmbiente . "/cancelado/";
            $nome_arquivo = $notafiscal->nota->chave . "-nfe.xml";
            if (!file_exists($path)) {
                mkdir($path, 07777, true);
            }

            $stdCl = new Standardize($response);
            $std = $stdCl->toStd();
            $xJust = $justificativa;
            $nProt = $std->protNFe->infProt->nProt;

            $response = self::$tools->sefazCancela($chave, $xJust, $nProt);
            $stdCl = new Standardize($response);
            $std = $stdCl->toStd();

            if ($std->cStat != 128) {
                //erro registrar e voltar
                $retorno->tem_erro = true;
                $retorno->titulo = "O lote nem foi processado, houve um problema ";
                $retorno->erro = $std->xMotivo;
                return $retorno;
            } else {
                $cStat = $std->retEvento->infEvento->cStat;
                if ($cStat == '101' || $cStat == '135' || $cStat == '155') {
                    $xml_cancelado = Complements::toAuthorize(self::$tools->lastRequest, $response);
                    file_put_contents($path . $nome_arquivo, $xml_cancelado);
                    chmod($path, 07777);
                    Nfe::where("id", $notafiscal->nota->id)->update(["status_id" => config("constantes.status.CANCELADO")]);

                    $retorno->tem_erro = false;
                    $retorno->titulo = "Nota cancelada com sucesso";
                    $retorno->erro = "";
                    $retorno->resultado = $std;
                    return $retorno;
                } else {
                    $motivo = $std->retEvento->infEvento->xMotivo ?? "01: Não foi Possível Fazer o Cancelamento";
                    $retorno->tem_erro = true;
                    $retorno->titulo = "01: Não foi Possível Fazer o Cancelamento";
                    $retorno->erro = $motivo;
                    return $retorno;
                }
            }
        } catch (\Exception $e) {
            $retorno->tem_erro = true;
            $retorno->titulo = "02: Não foi Possível Fazer o Cancelamento";
            $retorno->erro = $e->getMessage();
            return $retorno;
        }

        return $retorno;
    }

    public static function cancelarNfcePorChave($justificativa, $notafiscal)
    {
        self::config($notafiscal);
        $retorno = new \stdClass();
        try {
            $chave = $notafiscal->nota->chave;
            $response = self::$tools->sefazConsultaChave($chave);
            $path = "storage/" . self::$pastaEmpresa . "/xml/nfce/" . self::$pastaAmbiente . "/cancelado/";
            $nome_arquivo = $notafiscal->nota->chave . "-nfce.xml";
            if (!file_exists($path)) {
                mkdir($path, 07777, true);
            }

            $stdCl = new Standardize($response);
            $std = $stdCl->toStd();
            $xJust = $justificativa;
            $nProt = $notafiscal->nota->protocolo;

            $response = self::$tools->sefazCancela($chave, $xJust, $nProt);
            $stdCl = new Standardize($response);
            $std = $stdCl->toStd();

            if ($std->cStat != 128) {
                //erro registrar e voltar
                $retorno->tem_erro = true;
                $retorno->titulo = "O lote nem foi processado, houve um problema ";
                $retorno->erro = $std->xMotivo;
                return $retorno;
            } else {
                $cStat = $std->retEvento->infEvento->cStat;
                if ($cStat == '101' || $cStat == '135' || $cStat == '155') {
                    $xml_cancelado = Complements::toAuthorize(self::$tools->lastRequest, $response);
                    file_put_contents($path . $nome_arquivo, $xml_cancelado);
                    chmod($path, 07777);
                    Nfce::where("id", $notafiscal->nota->id)->update(["status_id" => config("constantes.status.CANCELADO")]);

                    $retorno->tem_erro = false;
                    $retorno->titulo = "Nota cancelada com sucesso";
                    $retorno->erro = "";
                    $retorno->resultado = $std;
                    return $retorno;
                } else {
                    $motivo = $std->retEvento->infEvento->xMotivo ?? "01: Não foi Possível Fazer o Cancelamento";
                    $retorno->tem_erro = true;
                    $retorno->titulo = "01: Não foi Possível Fazer o Cancelamento";
                    $retorno->erro = $motivo;
                    return $retorno;
                }
            }
        } catch (\Exception $e) {
            $retorno->tem_erro = true;
            $retorno->titulo = "02: Não foi Possível Fazer o Cancelamento";
            $retorno->erro = $e->getMessage();
            return $retorno;
        }

        return $retorno;
    }

    public static function consultarNfe($notafiscal)
    {
        self::config($notafiscal);
        $retorno = new \stdClass();
        try {

            $chave = $notafiscal->nota->chave;
            $response = self::$tools->sefazConsultaChave($chave);
            $stdCl = new Standardize($response);
            //nesse caso $std irá conter uma representação em stdClass do XML
            $std = $stdCl->toStd();
            $retorno->tem_erro = false;
            $retorno->titulo = "Consulta retornada com sucesso";
            $retorno->erro = "";
            $retorno->resultado = $std;


        } catch (\Exception $e) {
            $retorno->tem_erro = true;
            $retorno->titulo = "Erro ao consultar a nota na Sefaz";
            $retorno->erro = $e->getMessage();
        }

        return $retorno;
    }


}

