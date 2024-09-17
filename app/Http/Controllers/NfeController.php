<?php

namespace App\Http\Controllers;

use App\Models\Nfe;
use App\Services\NfeService;
use App\Services\NotaFiscalService;
use Illuminate\Http\Request;
use PHPMailer\PHPMailer\PHPMailer;


class NfeController extends Controller
{
    public function transmitirPelaVenda($id_venda)
    {
        $retorno = new \stdClass();
        $nfe = Nfe::where("venda_id", $id_venda)->first();
        if (!$nfe) {
            $retorno->tem_erro = true;
            $retorno->titulo = "Erro 10";
            $retorno->erro = "registro de NFE não encontrado";
            return response()->json($retorno, 201);
            exit;
        }

        $retorno = $this->transmitirNfe($nfe->id);
        if ($retorno->tem_erro) {
            return response()->json($retorno, 201);
        } else {
            return response()->json($retorno, 200);
        }
    }

    public function transmitirPelaNfe($id_nfe)
    {
        $retorno = new \stdClass();
        $nfe = Nfe::where("id", $id_nfe)->first();

        //return response()->json($nfe);

        if (!$nfe) {
            $retorno->tem_erro = true;
            $retorno->titulo = "Erro 10";
            $retorno->erro = "registro de NFE não encontrado";
            return response()->json($retorno, 200);
            exit;
        }
        $retorno = $this->transmitirNfe($id_nfe);
        if ($retorno->tem_erro) {
            return response()->json($retorno, 201);
        } else {
            return response()->json($retorno, 200);
        }
    }

    private function transmitirNfe($id_nfe)
    {
        $notafiscal = (object)NotaFiscalService::prepararNfe($id_nfe);

        if ($notafiscal->nota->status_id == config('constantes.status.EM_PROCESSAMENTO')) {
            $protocolo = NfeService::consultarProcessamento($notafiscal);
            return $protocolo;
            exit;
        }

        $xml = NfeService::gerarNfe($notafiscal);
        if (!$xml->tem_erro) {
            $xml_assinado = NfeService::assinarXml($xml->xml, $xml->chave, $notafiscal);
            if (!$xml_assinado->tem_erro) {
                $envio = NfeService::enviarXML($xml_assinado->xml, $xml->chave, $notafiscal);
                if (!$envio->tem_erro) {
                    $i = 0;
                    do {
                        sleep(3);
                        $i++;
                        $protocolo = NfeService::consultarPorRecibo($xml_assinado->xml, $xml->chave, $envio->recibo, $notafiscal);
                    } while ($protocolo->status_id == config('constantes.status.EM_PROCESSAMENTO') && $i < 3);

                    return $protocolo;
                } else {
                    return $envio;
                }
            } else {
                return $xml_assinado;
            }
        } else {
            return $xml;
        }
    }


    public function transmitir(Request $request)
    {
        $notafiscal = new \stdClass();

        $notafiscal->nota = (object)$request->nota;
        $notafiscal->emitente = (object)$request->emitente;
        $notafiscal->destinatario = (object)$request->destinatario;
        $notafiscal->itens = (object)$request->itens;
        $notafiscal->transportadora = (object)$request->transportadora;
        $notafiscal->fatura = (object)$request->fatura;
        $notafiscal->duplicatas = (object)$request->duplicatas;
        $notafiscal->pag = (object)$request->pag;
        $notafiscal->detalhePagamento = (object)$request->detalhePagamento;
        $notafiscal->intermediador = (object)$request->intermediador;
        $notafiscal->responsavel_tecnico = (object)$request->responsavel_tecnico;

        $xml = NfeService::gerarNfe($notafiscal);
        if (!$xml->tem_erro) {
            $xml_assinado = NfeService::assinarXml($xml->xml, $xml->chave, $notafiscal);
            if (!$xml_assinado->tem_erro) {
                $envio = NfeService::enviarXML($xml_assinado->xml, $xml->chave, $notafiscal);
                if (!$envio->tem_erro) {
                    $protocolo = NfeService::consultarPorRecibo($xml_assinado->xml, $xml->chave, $envio->recibo, $notafiscal);
                    return response()->json($protocolo, 200);
                } else {
                    return response()->json($envio, 201);
                }
            } else {
                return response()->json($xml_assinado, 201);
            }
        } else {
            return response()->json($xml, 201);
        }
    }

    public function lerXml($id_nfe)
    {
        $nfe = Nfe::find($id_nfe);
        $empresa = $nfe->empresa;
        $pastaAmbiente = ($nfe->tpAmb == "1") ? "producao" : "homologacao";
        $path = "storage/" . $nfe->empresa->pasta . "/xml/nfe/" . $pastaAmbiente . "/temporarias/" . $nfe->chave . "-nfe.xml";

        if (!file_exists($path)) {
            $notafiscal = (object)NotaFiscalService::prepararNfe($id_nfe);
            $nfe = NfeService::gerarNfe($notafiscal);

        }

        $path = "storage/" . $empresa->pasta . "/xml/nfe/" . $pastaAmbiente . "/temporarias/" . $nfe->chave . "-nfe.xml";
        if (file_exists($path)) {
            header("Content-type: text/xml; charset=UTF-8");
            header('Content-Disposition: attachment; filename=' . $nfe->chave . '.xml');
            header('Pragma: no-cache');
            readfile($path);
        } else {
            return response()->json("XML não foi encontrado", 401);
        }

    }

    public function baixarXml($chave)
    {
        $nfe = Nfe::where("chave", $chave)->first();
        $pastaAmbiente = ($nfe->tpAmb == "1") ? "producao" : "homologacao";
        $path = "storage/" . $nfe->empresa->pasta . "/xml/nfe/" . $pastaAmbiente . "/autorizadas/" . $chave . "-nfe.xml";

        if (file_exists($path)) {
            header("Content-type: text/xml; charset=UTF-8");
            header('Content-Disposition: attachment; filename=' . $chave . '.xml');
            header('Pragma: no-cache');
            readfile($path);
        } else {
            return response()->json("XML não foi encontrado", 401);
        }
    }

    public function baixarPdf($chave)
    {
        $danfe = NfeService::danfe($chave);
        if (!$danfe->tem_erro) {
            return response($danfe->pdf)->header("Content-type", "application/pdf");
        } else {
            return response()->json("erro", 401);
        }
    }

    public function imprimirDanfePelaChave($chave)
    {
        $danfe = NfeService::danfe($chave);
        if (!$danfe->tem_erro) {
            return response($danfe->pdf)->header("Content-type", "application/pdf");
        } else {
            return response()->json("erro", 401);
        }
    }

    public function imprimirDanfePelaNfe($id_nfe)
    {
        $nfe = Nfe::where("id", $id_nfe)->first();
        $danfe = NfeService::danfe($nfe->chave);
        if (!$danfe->tem_erro) {
            return response($danfe->pdf)->header("Content-type", "application/pdf");
        } else {
            return response()->json("erro", 401);
        }
    }

    public function simularDanfe($id_nfe)
    {
        $retorno = new \stdClass();
        $notafiscal = (object)NotaFiscalService::prepararNfe($id_nfe);

        $xml = NfeService::gerarNfe($notafiscal);

        if ($xml->tem_erro) {
            $retorno->tem_erro = true;
            $retorno->erro = $xml->erro;
            return response()->json($xml, 201);
        }


        $danfe = NfeService::simulaDanfe($xml->xml);
        if (!$danfe->tem_erro) {
            return response($danfe->pdf)->header("Content-type", "application/pdf");
        } else {
            return response()->json("erro", 401);
        }
    }

    public function imprimirDanfePelaVenda($id_venda)
    {
        $nfe = Nfe::where("venda_id", $id_venda)->first();
        $danfe = NfeService::danfe($nfe->chave);
        if (!$danfe->tem_erro) {
            return response($danfe->pdf)->header("Content-type", "application/pdf");
        } else {
            return response()->json("erro", 401);
        }
    }

    public function imprimirCce($id_nfe)
    {
        $retorno = NfeService::cce($id_nfe);
        if (!$retorno->tem_erro) {
            return response($retorno->pdf)->header("Content-type", "application/pdf");
        } else {
            return response()->json($retorno);
        }
    }

    public function imprimircancelado($id_nfe)
    {
        $retorno = NfeService::imprimirCancelado($id_nfe);
        if (!$retorno->tem_erro) {
            return response($retorno->pdf)->header("Content-type", "application/pdf");
        } else {
            return response()->json($retorno);
        }
    }

    public function danfe($chave)
    {
        $danfe = NfeService::danfe($chave);
        if (!$danfe->tem_erro) {
            return response($danfe->pdf)->header("Content-type", "application/pdf");
        } else {
            return response()->json($danfe);
        }
    }

    public function xml($chave)
    {
        $path = "storage/xml/nfe/homologacao/temporarias/" . $chave . "-nfe.xml";
        if (file_exists($path)) {
            header("Content-type: text/xml; charset=UTF-8");
            header('Content-Disposition: attachment; filename=' . $chave . '.xml');
            header('Pragma: no-cache');
            readfile($path);
        } else {
            return response()->json("XML não foi encontrado", 401);
        }

    }

    public function email(Request $request)
    {
        $req = $request->all();
        $email = $req["email"];

        $nfe = Nfe::where("id", $req["id_nfe"])->first();
        $pastaAmbiente = ($nfe->tpAmb == "1") ? "producao" : "homologacao";
        $chave = $nfe->chave;
        $retorno = new \stdClass();
        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->CharSet = 'UTF-8';
            $mail->SMTPDebug = false;
            $mail->isSMTP();
            $mail->Host = env('MAIL_HOST');
            $mail->SMTPAuth = true;
            $mail->Username = env('MAIL_USERNAME');
            $mail->Password = env('MAIL_PASSWORD');
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            $mail->setFrom(env('MAIL_USERNAME'), "Nota Fiscal");
            $mail->addAddress($email);

            $path = "storage/" . $nfe->empresa->pasta . "/xml/nfe/" . $pastaAmbiente . "/autorizadas/" . $chave . "-nfe.xml";

            if (file_exists($path)) {
                $mail->addAttachment($path);
            }
            //PDF
            $pdf = NfeService::danfe($chave);
            if (!$pdf->tem_erro) {
                $path_pdf = "storage/" . $nfe->empresa->pasta . "/pdf/nfe/";
                $nome_arquivo = $chave . ".pdf";
                if (!file_exists($path_pdf)) {
                    mkdir($path_pdf, 07777, true);
                }

                file_put_contents($path_pdf . $nome_arquivo, $pdf->pdf);
                $mail->addAttachment($path_pdf . $nome_arquivo);
            }

            $mail->isHTML(true);
            $mail->Subject = "Emissão de NFe: " . $chave;
            $mail->Body = "Olá segue em anexo DANFE e XML NFe " . $chave;
            if ($mail->send()) {
                $retorno->tem_erro = false;
                $retorno->titulo = "Email enviado com sucesso !!";
                $retorno->erro = "";
                return response()->json($retorno);
            } else {
                $retorno->tem_erro = true;
                $retorno->titulo = "Não foi possível enviar o email !!";
                $retorno->erro = "Não foi possível enviar o email !!";
                return response()->json($retorno);
            }

        } catch (\Exception $e) {
            $retorno->tem_erro = true;
            $retorno->titulo = "Erro ao Enviar Email";
            $retorno->erro = $e->getMessage();
            return response()->json($retorno);
        }
    }

    public function cartaCorrecao(Request $request)
    {
        $req = (object)$request->all();
        $notafiscal = (object)NotaFiscalService::prepararNfe($req->id);
        $correcao = NfeService::cartaCorrecao($req->correcao, $notafiscal);
        return response()->json($correcao);
    }

    public function inutilizarNfe(Request $request)
    {
        $req = (object)$request->all();
        $emitente = new \stdClass();
        $emitente->CNPJ = $req->cnpj;

        $nota = new \stdClass();
        $nota->tpAmb = $req->tpAmb;

        $notafiscal = new \stdClass();
        $notafiscal->emitente = $emitente;
        $notafiscal->nota = $nota;

        $inutilizacao = NfeService::inutilizar($req, $notafiscal);
        return response()->json($inutilizacao);
    }

    public function consultarNfe($id_nfe)
    {
        $nota = Nfe::find($id_nfe);
        $emitente = new \stdClass();
        $emitente->CNPJ = $nota->em_CNPJ;

        $notafiscal = new \stdClass();
        $notafiscal->emitente = $emitente;
        $notafiscal->nota = $nota;

        $retorno = NfeService::consultarNfe($notafiscal);
        return response()->json($retorno);
    }

    public function cancelarNfe(Request $request)
    {
        $req = (object)$request->all();
        $notafiscal = (object)NotaFiscalService::prepararNfe($req->id);
        $retorno = NfeService::cancelarPorChave($req->justificativa, $notafiscal);
        return response()->json($retorno);
    }

    public function consultar(Request $request)
    {
        $config = (object)$request->all();
        $consulta = NfeService::consultarPorChave($config);

        if (!$consulta->tem_erro) {
            return response()->json($consulta->resultado, 200);
        } else {
            return response()->json('Consulta não encontrada', 404);
        }
    }


    public function cancelar_e_imprimir(Request $request)
    {
        $config = (object)$request->all();
        $retorno = NfeService::cancelarPorChave($config, $config->justificativa);
        if (!$retorno->tem_erro) {
            $cancelado = NfeService::imprimirCancelaPorChave($config->chave);
            if (!$cancelado->tem_erro) {
                return response($cancelado->pdf)->header("Content-type", "application/pdf");
            } else {
                return response()->json("Não foi possível gerar o pdf", 401);
            }
        } else {
            return response()->json('Consulta não encontrada', 404);
        }
    }


}
