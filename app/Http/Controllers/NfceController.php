<?php

namespace App\Http\Controllers;

use App\Models\NaturezaOperacao;
use App\Models\Nfce;
use App\Models\PdvVenda;
use App\Models\Tributacao;
use App\Services\NfceService;
use App\Services\NfeService;
use App\Services\NotaFiscalService;
use Illuminate\Http\Request;
use PHPMailer\PHPMailer\PHPMailer;

class NfceController extends Controller
{
    public function gerarNfcePelaVenda($id)
    {
        $pdvvenda = PdvVenda::find($id);
        $nfce = Nfce::where("venda_id", $id)->first();
        $natureza_operacao = NaturezaOperacao::where("padrao", config('constantes.padrao_natureza.PDV'))->first();
        $tributacao = Tributacao::where(["natureza_operacao_id" => $natureza_operacao->id, "padrao" => "S"])->first();

        if (!$nfce) {
            PdvVenda::inserirNfcePelaVenda($pdvvenda, $natureza_operacao, $tributacao);
        }
    }

    public function transmitirPelaComanda($comanda_id)
    {
        $nfce = Nfce::where("comanda_id", $comanda_id)->first();

        if (!$nfce) {
            echo json_encode("-1");
            exit;
        }
        return $this->transmitirNfce($nfce);
    }

    public function transmitirPelaVenda($id_venda)
    {
        $nfce = Nfce::where("venda_id", $id_venda)->first();

        if (!$nfce) {
            echo json_encode("-1");
            exit;
        }
        return $this->transmitirNfce($nfce);
    }

    private function transmitirNfce($nfce)
    {
        $notafiscal = NotaFiscalService::prepararNfce($nfce);

        $xml = NfceService::gerarNfce($notafiscal);
        if (!$xml->tem_erro) {
            $xml_assinado = NfceService::assinarXml($xml->xml, $xml->chave, $notafiscal);
            if (!$xml_assinado->tem_erro) {
                $envio = NfceService::enviarXML($xml_assinado->xml, $xml->chave, $notafiscal);
                if (!$envio->tem_erro) {
                    return response()->json($envio, 200);
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

    public function consultar(Request $request)
    {
        dd($request);
        $notafiscal = NotaFiscalService::prepararNfe($request->id, 65);
        $consulta = NfceService::consultar($notafiscal);

        if (!$consulta->tem_erro) {
            return response()->json($consulta->resultado, 200);
        } else {
            return response()->json('Consulta não encontrada', 404);
        }
    }

    public function imprimirDanfcePelaVenda($id)
    {
        $nfce = Nfce::where("pdvvenda_id", $id)->first();
        $danfce = NfceService::danfce($nfce->chave);
        if (!$danfce->tem_erro) {
            return response($danfce->pdf)->header("Content-type", "application/pdf");
        } else {
            return response()->json("erro", 401);
        }
    }

    public function imprimircancelado($id)
    {
        $retorno = NfceService::imprimirCancelado($id);
        if (!$retorno->tem_erro) {
            return response($retorno->pdf)->header("Content-type", "application/pdf");
        } else {
            return response()->json($retorno);
        }
    }

    public function danfce($chave)
    {
        $danfce = NfceService::danfce($chave);
        if (!$danfce->tem_erro) {
            return response($danfce->pdf)->header("Content-type", "application/pdf");
        } else {
            return response()->json("erro", 401);
        }
    }

    public function baixarXml($chave)
    {
        $nfe = Nfce::where("chave", $chave)->first();
        $pastaAmbiente = ($nfe->tpAmb == "1") ? "producao" : "homologacao";
        $path = "storage/" . $nfe->empresa->pasta . "/xml/nfce/" . $pastaAmbiente . "/autorizadas/" . $chave . "-nfce.xml";

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
        $danfce = NfceService::danfce($chave);
        if (!$danfce->tem_erro) {
            return response($danfce->pdf)->header("Content-type", "application/pdf");
        } else {
            return response()->json("erro", 401);
        }
    }

    public function email(Request $request)
    {
        $req = $request->all();
        $email = $req["email"];

        $nfe = Nfce::where("id", $req["id_nfe"])->first();
        $pastaAmbiente = ($nfe->tpAmb == "1") ? "producao" : "homologacao";
        $chave = $nfe->chave;

        $mail = new PHPMailer(true);
        try {
            //Server settings
            $mail->CharSet = 'UTF-8';
            $mail->SMTPDebug = false;
            $mail->isSMTP();
            $mail->Host = env('MAIL_SMTP');
            $mail->SMTPAuth = true;
            $mail->Username = env('MAIL_USER');
            $mail->Password = env('MAIL_PASS');
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            $mail->setFrom(env('MAIL_USER'), env('MAIL_NAME'));
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
                return response()->json("Email enviado com sucesso !!", 200);
            } else {
                return response()->json("Não foi possível enviar o email", 401);
            }


        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 401);
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


        $xml = NfceService::gerarNfce($notafiscal);
        if (!$xml->tem_erro) {
            $xml_assinado = NfceService::assinarXml($xml->xml, $xml->chave, $notafiscal);
            if (!$xml_assinado->tem_erro) {
                $envio = NfceService::enviarXML($xml_assinado->xml, $xml->chave, $notafiscal);
                if (!$envio->tem_erro) {
                    return response()->json($envio, 200);
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

    public function cancelarNfce(Request $request)
    {
        $req = (object)$request->all();
        $notafiscal = (object)NotaFiscalService::prepararNfe($req->id, 65);
        $retorno = NfceService::cancelarNfcePorChave($req->justificativa, $notafiscal);
        return response()->json($retorno);
    }
}
