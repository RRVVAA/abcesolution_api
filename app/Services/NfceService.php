<?php

namespace App\Services;

use App\Models\Emitente;
use App\Models\Nfce;
use Illuminate\Support\Facades\Storage;
use NFePHP\Common\Certificate;
use NFePHP\DA\NFe\Daevento;
use NFePHP\DA\NFe\Danfce;
use NFePHP\NFe\Complements;
use NFePHP\NFe\Make;
use NFePHP\NFe\Tools;
use NFePHP\NFe\Common\Standardize;
use App\Models\CertificadoDigital;
use App\Models\NfceXml;

class NfceService
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
            "CSC" => $emitente->csc,
            "CSCid" => $emitente->csc_id,
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
        self::$tools->model('65');
    }

    public static function gerarNfce($notafiscal)
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
        NfceTagService::infNfe($nfe);
        NfceTagService::identificacao($nfe, $notafiscal);
        NfceTagService::emitente($nfe, $notafiscal->emitente);

        if ($notafiscal->nota->cliente_cpf || $notafiscal->nota->cliente_cnpj)
            NfceTagService::destinatario($nfe, $notafiscal->nota->cliente_cpf, $notafiscal->nota->cliente_cnpj);

        $cont = 1;
        foreach ($notafiscal->itens as $item) {

            NfceTagService::dadosProduto($cont, $nfe, (object)$item["produto"]);
            NfceTagService::imposto($cont, $nfe, (object)$item["imposto"]);
            NfceTagService::icms($cont, $nfe, (object)$item["icms"]);

            if (isset($item->ipi->vIPI))
                NfceTagService::ipi($cont, $nfe, (object)$item["ipi"]);

            NfceTagService::pis($cont, $nfe, (object)$item["pis"]);
            NfceTagService::cofins($cont, $nfe, (object)$item["cofins"]);

            if (isset($item->observacao->infAdProd))
                NfceTagService::observacao($cont, $nfe, (object)$item["observacao"]);
            $cont++;
        }

        NfceTagService::totais($nfe, $notafiscal->nota);
        NfceTagService::transp($nfe, $notafiscal->nota);
        if (isset($notafiscal->transportadora->xNome))
            NfceTagService::transportadora($nfe, $notafiscal->transportadora);

        /*  if(count($notafiscal->cobranca->duplicatas) > 0){
              if($notafiscal->nota->finNFe!= 3 && $notafiscal->nota->finNFe!= 4 ){
                  NfceTagService::cobranca($nfe, $notafiscal->cobranca);
              }
          }  */

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
                NfceTagService::pagamentos($nfe, $notafiscal->pagamentos);
            }
        } else {
            $std = new \stdClass();
            $std->indPag = '0';
            $std->tPag = '01';
            $std->vPag = $notafiscal->nota->vNF;
            $nfe->tagdetPag($std);
        }
        NfceTagService::infRespTec($nfe, $notafiscal->nota);
        //  NfceTagService::intermed($nfe, $notafiscal->nota);

        if (($notafiscal->nota->infAdFisco) || ($notafiscal->nota->infCpl))
            NfceTagService::infAdic($nfe, $notafiscal->nota);

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

                $path = "storage/" . self::$pastaEmpresa . "/xml/nfce/" . self::$pastaAmbiente . "/temporarias/";
                $nome_arquivo = $chave . "-nfce.xml";
                if (!file_exists($path)) {
                    mkdir($path, 07777, true);
                }
                file_put_contents($path . $nome_arquivo, $xml);
                chmod($path, 07777);

                Nfce::where("id", $notafiscal->nota->id)->update(["chave" => $chave]);
                $tem = NfceXml::where("nfce_id", $notafiscal->nota->id)->first();
                if (!$tem) {
                    NfceXml::Create(["nfce_id" => $notafiscal->nota->id, "xml" => $nfe->getXML(), "chave" => $chave, "empresa_id" => $notafiscal->nota->empresa_id]);
                } else {
                    $tem->update(["xml" => $nfe->getXML(), "chave" => $chave]);
                }
                $retorno->tem_erro = false;
                $retorno->titulo = "arquivo XML gerado com Sucesso";
                $retorno->erro = "";
                $retorno->chave = $chave;
                $retorno->xml = $xml;

            } else {
                Nfce::where("id", $notafiscal->nota->id)->update(["status_id" => config('constantes.status.ERRO_AO_GERAR_XML')]);
                $retorno->tem_erro = true;
                $retorno->titulo = "Não foi possível gerar o XML";
                $retorno->erro = $nfe->getErrors();
            }
        } catch (\Exception $e) {
            $retorno->tem_erro = true;
            $retorno->titulo = "Não foi possível gerar o arquivo XML";
            if ($nfe->getErrors() != null) {
                $retorno->erro = $nfe->getErrors();
            } else {
                $retorno->erro = $e->getMessage() . $e->getLine() . $e->getFile();
            }
        }
        return $retorno;
    }


    public static function assinarXml($xml, $chave, $notafiscal)
    {
        self::config($notafiscal);
        $retorno = new \stdClass();
        try {
            $response = self::$tools->signNFe($xml);

            $path = "storage/" . self::$pastaEmpresa . "/xml/nfce/" . self::$pastaAmbiente . "/assinadas/";
            $nome_arquivo = $chave . "-nfce.xml";

            if (!file_exists($path)) {
                mkdir($path, 07777, true);
            }

            file_put_contents($path . $nome_arquivo, $response);
            chmod($path, 07777);

            Nfce::where("id", $notafiscal->nota->id)->update(["status_id" => config('constantes.status.ERRO_AO_GERAR_XML')]);
            $retorno->tem_erro = false;
            $retorno->titulo = "XML assinado com sucesso";
            $retorno->erro = "";
            $retorno->xml = $response;

        } catch (\Exception $e) {
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
            $xmlResp = self::$tools->sefazEnviaLote([$xml], $idLote, 1);
            //transforma o xml de retorno em um stdClass
            $st = new Standardize();
            $std = $st->toStd($xmlResp);

            if ($std->cStat != 103 && $std->cStat != 104) {
                $retorno->tem_erro = true;
                $retorno->titulo = "Não foi possível enviar o XML para a Sefaz";
                $retorno->erro = "[$std->cStat] $std->xMotivo";
                return $retorno;
            }
            sleep(5);
            try {
                $xml_autorizado = Complements::toAuthorize($xml, $xmlResp);
                $path = "storage/" . self::$pastaEmpresa . "/xml/nfce/" . self::$pastaAmbiente . "/autorizadas/";
                $nome_arquivo = $chave . "-nfce.xml";
                $protocolo = $std->protNFe->infProt->nProt;

                if (!file_exists($path)) {
                    mkdir($path, 07777, true);
                }

                file_put_contents($path . $nome_arquivo, $xml_autorizado);
                chmod($path, 07777);

                Nfce::where("id", $notafiscal->nota->id)->update(["protocolo" => $protocolo, "status_id" => config('constantes.status.AUTORIZADO')]);

                $retorno->tem_erro = false;
                $retorno->titulo = "XML autorizado com sucesso";
                $retorno->erro = "";
                $retorno->chave = $chave;
                $retorno->recibo = null;
                $retorno->protocolo = $protocolo;
                $retorno->xml = $xmlResp;

            } catch (\Exception $e) {
                $retorno->tem_erro = true;
                $retorno->titulo = "Erro ao enviar o lote para a Sefaz";
                $retorno->erro = $e->getMessage();
            }


        } catch (\Exception $e) {
            $retorno->tem_erro = true;
            $retorno->titulo = "Erro ao enviar o lote para a Sefaz";
            $retorno->erro = $e->getMessage();
        }

        return $retorno;
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
                $retorno->tem_erro = true;
                $retorno->titulo = "Protocolo ainda não disponível";
                $retorno->erro = "O lote ainda não foi processado";
                return $retorno;
            }
            if ($std->cStat == '105') { //lote em processamento
                $retorno->tem_erro = true;
                $retorno->titulo = "Protocolo sendo processado";
                $retorno->erro = "Lote em processamento, tente, mais tarde";
                return $retorno;
            }

            if ($std->cStat == '104') { //lote processado (tudo ok)
                if ($std->protNFe->infProt->cStat == '100') { //Autorizado o uso da NF-e
                    $xml_autorizado = Complements::toAuthorize($xml, $xmlResp);

                    $path = "storage/xml/nfce/" . self::$pastaAmbiente . "/autorizadas/" . $chave . "-nfce.xml";
                    file_put_contents($path, $xml_autorizado);
                    chmod($path, 07777);

                    $retorno->tem_erro = false;
                    $retorno->titulo = "XML autorizado com sucesso";
                    $retorno->erro = "";
                    $retorno->recibo = $recibo;
                    $retorno->chave = $chave;
                    $retorno->protocolo = $std->protNFe->infProt->nProt;
                    $retorno->xml = $xmlResp;

                } elseif (in_array($std->protNFe->infProt->cStat, ["110", "301", "302"])) { //DENEGADAS
                    $retorno->tem_erro = true;
                    $retorno->titulo = "Nota Denegada";
                    $retorno->erro = $std->protNFe->infProt->cStat . ":" . $std->protNFe->infProt->xMotivo;
                    $retorno->protocolo = $std->protNFe->infProt->nProt;
                    $retorno->xml = $xmlResp;
                    $retorno->cstat = $std->protNFe->infProt->cStat;
                    return $retorno;
                } else { //não autorizada (rejeição)
                    $retorno->tem_erro = true;
                    $retorno->titulo = "Nota Rejeitada";
                    $retorno->erro = $std->protNFe->infProt->cStat . ":" . $std->protNFe->infProt->xMotivo;
                    $retorno->cstat = $std->protNFe->infProt->cStat;
                    return $retorno;
                }
            } else { //outros erros possíveis
                $retorno->tem_erro = true;
                $retorno->titulo = "Nota Rejeitada";
                $retorno->erro = $std->cStat . ":" . $std->xMotivo;
                $retorno->cstat = $std->cStat;
                return $retorno;
            }

        } catch (\Exception $e) {
            $retorno->tem_erro = true;
            $retorno->titulo = "Erro ao consultar a nota na Sefaz";
            $retorno->erro = $e->getMessage();
        }

        return $retorno;
    }

    public static function danfce($chave)
    {
        $nfce = Nfce::where("chave", $chave)->first();

        $pastaAmbiente = ($nfce->tpAmb == "1") ? "producao" : "homologacao";
        $path01 = "storage/".$nfce->empresa->pasta . "/xml/nfce/" . $pastaAmbiente . "/autorizadas/" . $chave . "-nfce.xml";
        $req = file_get_contents($path01);

        $path02 = "storage/".$nfce->empresa->pasta . "/xml/nfce/" . $pastaAmbiente . "/cancelado/" . $chave . "-nfce.xml";
        if (file_exists($path02)) {
            $res = file_get_contents($path02);
            $xml = Complements::cancelRegister($req, $res);
        } else {
            $xml = $req;
        }

        $retorno = new \stdClass();
        try {
            $danfce = new Danfce($xml);
            $pdf = $danfce->render();

            $retorno->tem_erro = false;
            $retorno->titulo = "Pdf gerado com sucesso";
            $retorno->erro = "";
            $retorno->chave = $chave;
            $retorno->pdf = $pdf;
        } catch (\Exception $e) {
            $retorno->tem_erro = true;
            $retorno->titulo = "Erro gerar o PDF";
            $retorno->erro = $e->getMessage();
        }

        return $retorno;
    }

    public static function consultarPorChave($config)
    {
        self::config2($config);
        $retorno = new \stdClass();
        try {

            $chave = $config->chave;
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

    public static function cancelarPorChave($config, $justificativa)
    {
        self::config2($config);
        $retorno = new \stdClass();
        try {

            $chave = $config->chave;
            $response = self::$tools->sefazConsultaChave($chave);

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
                $retorno->erro = "O lote nem foi processado, houve um problema ";
                $retorno->resultado = $std->xMotivo;
                return $retorno;
            } else {
                $cStat = $std->retEvento->infEvento->cStat;
                if ($cStat == '101' || $cStat == '135' || $cStat == '155') {
                    $xml_cancelado = Complements::toAuthorize(self::$tools->lastRequest, $response);
                    $path = "storage/xml/nfe/homologacao/canceladas/" . $chave . "-nfe.xml";
                    file_put_contents($path, $xml_cancelado);
                    chmod($path, 07777);

                    $retorno->tem_erro = false;
                    $retorno->titulo = "Nota cancelada com sucesso";
                    $retorno->erro = "";
                    $retorno->resultado = $std;
                    return $retorno;

                } else {
                    $retorno->tem_erro = true;
                    $retorno->titulo = "Erro ao consultar a nota na Sefaz";
                    $retorno->erro = $std;
                    return $retorno;
                }
            }
        } catch (\Exception $e) {
            $retorno->tem_erro = true;
            $retorno->titulo = "Erro ao consultar a nota na Sefaz";
            $retorno->erro = $e->getMessage();
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

            //$pathXML = self::$pastaEmpresa . "/xml/nfce/" . self::$pastaAmbiente . "/cancelado/".$notafiscal->nota->chave . "-nfce.xml";

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

                    /*$pathPut = Storage::disk('arquivos')->put($pathXML, file_get_contents($xml_cancelado));
                    Storage::disk('arquivos')->setVisibility($pathPut, 'public');*/

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

    private static function getEmitente()
    {
        return [
            'razao' => "Intelimax Comércio",
            'logradouro' => "Rua Barata Ribeiro",
            'numero' => "191",
            'complemento' => 'comJ',
            'bairro' => "Z0NA RURAL",
            'CEP' => "22040002",
            'municipio' => "Rio de Janeiro",
            'UF' => "RJ",
            'telefone' => '',
            'email' => 'mjailton@gmail.com'
        ];
    }

    public static function imprimirCancelaPorChave($chave)
    {
        $retorno = new \stdClass();
        $path = "storage/xml/nfe/homologacao/canceladas/" . $chave . "-nfe.xml";
        $xml = file_get_contents($path);
        try {
            $daevento = new Daevento($xml, self::getEmitente());
            $daevento->debugMode(true);
            $pdf = $daevento->render();
            $retorno->tem_erro = false;
            $retorno->titulo = "Pdf gerado com sucesso";
            $retorno->erro = "";
            $retorno->chave = $chave;
            $retorno->pdf = $pdf;
        } catch (\Exception $e) {
            $retorno->tem_erro = true;
            $retorno->titulo = "Erro gerar o PDF";
            $retorno->erro = $e->getMessage();
        }

        return $retorno;
    }

    public static function imprimirCancelado($id_nfe)
    {
        $nfe = Nfce::where("id", $id_nfe)->first();
        $pastaAmbiente = ($nfe->tpAmb == "1") ? "producao" : "homologacao";
        $path = "storage/" . $nfe->empresa->pasta . "/xml/nfce/" . $pastaAmbiente . "/cancelado/" . $nfe->chave . "-nfce.xml";
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

    public function inutilizar($config)
    {
        self::config2($config);
        $retorno = new \stdClass();
        try {

            $nSerie = $config->nSerie;
            $nIni = $config->nIni;
            $nFin = $config->nFin;
            $xJust = $config->justificativa;
            $response = self::$tools->sefazInutiliza($nSerie, $nIni, $nFin, $xJust);

            $stdCl = new Standardize($response);
            $std = $stdCl->toStd();

            $retorno->tem_erro = false;
            $retorno->titulo = "Nota Inutilizada com sucesso";
            $retorno->erro = "";
            $retorno->resultado = $std;

        } catch (\Exception $e) {
            $retorno->tem_erro = true;
            $retorno->titulo = "Erro ao inutilizar a nota";
            $retorno->erro = $e->getMessage();
        }

        return $retorno;
    }


}

