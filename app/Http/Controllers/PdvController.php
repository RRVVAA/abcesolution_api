<?php

namespace App\Http\Controllers;

use App\Models\Nfce;
use App\Services\NfceRequisitoService;
use App\Services\NfceService;
use App\Services\NotaFiscalService;


class PdvController extends Controller{
    public function verificarRequisito($empresa_id){
        $retorno = NfceRequisitoService::verificar($empresa_id);      
        return response()->json($retorno);
    }
    
    public function transmitirPelaNfce($id_nfce){
        $nfce = Nfce::where("id",$id_nfce)->first();
        if(!$nfce){
            echo json_encode("-1");
            exit;
        }
        return $this->transmitirNfce($nfce);
    }
    
    
    private function transmitirNfce($nfce){
        $notafiscal = NotaFiscalService::prepararNfce($nfce);
        
        $xml =  NfceService::gerarNfce($notafiscal);
        if(!$xml->tem_erro){
            $xml_assinado = NfceService::assinarXml($xml->xml, $xml->chave, $notafiscal);
            if(!$xml_assinado->tem_erro){
                $envio = NfceService::enviarXML($xml_assinado->xml, $xml->chave, $notafiscal) ;
                if(!$envio->tem_erro){
                    return response()->json($envio, 200);
                }else{
                    return response()->json($envio, 201);
                }
            }else{
                return response()->json($xml_assinado, 201);
            }
        }else{
            
            return response()->json($xml, 201);
        }
    }
    
    
   /* public function listaCaixa($id_empresa, $id_usuario){
        $lista = PdvCaixa::where(["empresa_id" =>$id_empresa,"usuario_abriu_id" =>$id_usuario ])->get();
        echo json_encode($lista);
    }
    
    public function listaCaixaAbertoPorUsuario($id_usuario, $id_empresa){
        $caixa = PdvCaixa::where("usuario_abriu_id", $id_usuario)
        ->where("status_id", config("constantes.status.ABERTO"))->get();
        echo json_encode($caixa);
    }
    
    public function listaVendaPorUsuario($id_empresa, $id_usuario){
        $lista = PdvVenda::where(["empresa_id" =>$id_empresa,"usuario_id" =>$id_usuario ])->get();
        echo json_encode($lista);
    }
    
    public function listaVendaPorCaixa($id_empresa, $id_caixa){
        $lista = PdvVenda::where(["empresa_id" =>$id_empresa,"caixa_id" =>$id_caixa ])->get();
        echo json_encode($lista);
    }
    
    public function getVenda($id_empresa, $id_venda){
        $lista = PdvVenda::where(["empresa_id" =>$id_empresa,"id" =>$id_venda ])->with("caixa")->with("itens")->with("itens.produto")->first();
        echo json_encode($lista);
    }    
    
    public function listaSangriaPorUsuario($id_empresa, $id_usuario){
        $lista = PdvSangria::where(["empresa_id" =>$id_empresa,"usuario_id" =>$id_usuario ])->get();
        echo json_encode($lista);
    }
    
    public function listaSangriaPorCaixa($id_empresa, $id_caixa){
        $lista = PdvSangria::where(["empresa_id" =>$id_empresa,"caixa_id" =>$id_caixa ])->get();
        echo json_encode($lista);
    }
    
    public function listaSuplementoPorUsuario($id_empresa, $id_usuario){
        $lista = PdvSuplemento::where(["empresa_id" =>$id_empresa,"usuario_id" =>$id_usuario ])->get();
        echo json_encode($lista);
    }
    
    public function listaSuplementoPorCaixa($id_empresa, $id_caixa){
        $lista = PdvSuplemento::where(["empresa_id" =>$id_empresa,"caixa_id" =>$id_caixa ])->get();
        echo json_encode($lista);
    }
    
    public function listaNumeroCaixa($id_empresa){  
        $lista = PdvCaixa::caixaNumeroNaoInseridas($id_empresa);
        echo json_encode($lista);
    }
    
    public function caixaDetalhes($id_empresa, $id_caixa){
        $caixa         = new \stdClass();
        $caixa->caixa  = PdvCaixa::where(["id" => $id_caixa, "empresa_id" =>$id_empresa])->first();
        $caixa->formas = CaixaService::listaSomaPorFormaPagto($id_caixa);
        $caixa->valores= CaixaService::valores($id_caixa);
        $caixa->vendas = PdvVenda::where("caixa_id",$id_caixa)->get();        
        echo json_encode($caixa);
    }
    
    public function verificaSeTemCaixaAbertoPorUsuario($id_usuario, $id_empresa){
        $caixa = PdvCaixa::where("usuario_abriu_id", $id_usuario)
                            ->where("empresa_id", $id_empresa)
                            ->where("status_id", config("constantes.status.ABERTO"))->first();        
        echo json_encode($caixa);
    }
   
    
    
    public function abrirCaixa(Request $request){
        try{
            $caixa  = PdvCaixa::Create($request->all());
            echo json_encode($caixa);
        } catch (\Exception $e) {
            echo "-1";
        }
    }
       
    public function salvarSangria(Request $request){
        $retorno = new \stdClass();        
        try{
            $retorno->tem_erro = false;
            $retorno->erro = "";
            $retorno->retorno = PdvSangria::Create($request->all());
            return response()->json($retorno);
        } catch (\Exception $e) {
            $retorno->tem_erro = true;
            $retorno->erro     = $e->getMessage();
            return response()->json($retorno);
        }
    }
    
    public function salvarSuplemento(Request $request){
        $retorno = new \stdClass();
        try{
            $retorno->tem_erro  = false;
            $retorno->erro      = "";
            $retorno->retorno   = PdvSuplemento::Create($request->all());
            return response()->json($retorno);
        } catch (\Exception $e) {
            $retorno->tem_erro  = true;
            $retorno->erro      = $e->getMessage();
            return response()->json($retorno);
        }
    }
    
    public function  salvarVenda(Request  $request){
        $retorno            = new \stdClass();
        try{
            $req            = $request->all();
            $dadosVenda     = ($req["venda"]) ?? null;
            $dadosItensVenda= ($req["itens"]) ?? null;
            $validaVenda    = VendaService::dadosVenda($dadosVenda);
            if($validaVenda!= ""){
                $retorno->tem_erro  = true;
                $retorno->titulo    = "Erro ao Inserir Venda";
                $retorno->erro      = $validaVenda;
                echo json_encode($retorno);
                exit;
            }
            
            $validaItensVenda = VendaService::dadosItemVenda($dadosItensVenda);
            if($validaItensVenda!= ""){
                $retorno->tem_erro  = true;
                $retorno->titulo    = "Erro ao Inserir Venda";
                $retorno->erro      = $validaItensVenda;
                echo json_encode($retorno);
                exit;
            }
            
            $venda = PdvVendaService::salvarVenda($request->all());
            $retorno->tem_erro  = false;
            $retorno->titulo    = "";
            $retorno->erro      = "";
            $retorno->retorno   = $venda;            
            echo json_encode($retorno);
            
        } catch (\Exception $e) {
            $retorno->tem_erro  = true;
            $retorno->titulo    = "Erro ao Inserir Venda";
            $retorno->erro      = $e->getMessage();
            echo json_encode($retorno);
        }
            
    }
    
    public function salvarVendaAntigo(Request $request){
        $retorno            = new \stdClass();
        try{ 
            $req            = $request->all();    
            $dadosNfce      = ($req["dadosNfce"]["nfce"]) ?? null;  
            $dadosItensNfce = ($req["dadosNfce"]["itens"]) ?? null; 
            $dadosVenda     = ($req["venda"]) ?? null;
            $dadosItensVenda= ($req["itens"]) ?? null;
        
            $validaVenda = VendaService::dadosVenda($dadosVenda);
            if($validaVenda!= ""){
                $retorno->tem_erro  = true;
                $retorno->titulo    = "Erro ao Inserir Venda";
                $retorno->erro      = $validaVenda;
                echo json_encode($retorno);
                exit;
            }
            
            $validaItensVenda = VendaService::dadosItemVenda($dadosItensVenda);
            if($validaItensVenda!= ""){
                $retorno->tem_erro  = true;
                $retorno->titulo    = "Erro ao Inserir Venda";
                $retorno->erro      = $validaItensVenda;
                echo json_encode($retorno);
                exit;
            }
           
            $validaNfe  = ValidacaoNfeService::dadosNFe($dadosNfce);           
            if($validaNfe!= ""){
                $retorno->tem_erro  = true;
                $retorno->titulo    = "Erro ao Inserir NFCE";
                $retorno->erro      = $validaNfe;
                echo json_encode($retorno);
                exit;
            } 
            
            $validaItensNfe = ValidacaoNfeService::dadosItemNfce($dadosItensNfce);
            if($validaItensNfe!= ""){
                $retorno->tem_erro  = true;
                $retorno->titulo    = "Erro ao Inserir Item da NFCE";
                $retorno->erro      = $validaItensNfe;
                echo json_encode($retorno);
                exit;
            }           
            
            $venda = PdvVendaService::salvarVenda($request->all());
            $retorno->tem_erro  = false;
            $retorno->titulo    = "";
            $retorno->erro      = "";
            $retorno->retorno   = $venda;
            
            echo json_encode($retorno); 
            
          } catch (\Exception $e) {
              $retorno->tem_erro  = true;
              $retorno->titulo    = "Erro ao Inserir Venda";
              $retorno->erro      = $e->getMessage();   
              echo json_encode($retorno); 
        }
    }
    
    public function getDadosParaGerarNfce(Request $request){      
        try {
            $emitente    = Emitente::where("empresa_id", $request->empresa_id)->first();
            $produtos      = array();
            foreach($request->itens as $i){
                $produto  = Produto::where("id",$i["codigo"])->with("tributacao")->first();
                $produto->qtde       = $i["quantidade"];
                $produto->valor      = $i["valor"];
                $produto->subtotal   = $i["valor"] * $i["quantidade"];
                $produtos[]          = $produto;
            }            
            echo json_encode(["emitente"=>$emitente,  "produtos" =>$produtos]);
        } catch (\Exception $e) {
            i($e->getMessage());
        }
    }
    
    public function getDadosParaGerarNfcePelaVenda($id_venda,$id_empresa){
        try {
            $emitente   = Emitente::where("empresa_id", $id_empresa)->first();
            $venda      = PdvVenda::where("id", $id_venda)->first();
            $itens      = PdvItemVenda::where("venda_id", $id_venda)->with("produto")->get();
            $tributacao = Tributacao::first();       
            echo json_encode(["emitente"=>$emitente, "pdvVenda" =>$venda,"itens"=>$itens,"tributacao"=>$tributacao]);
        } catch (\Exception $e) {
            i($e->getMessage());
        }
    }
    
    public function salvarNfcePelaVenda(Request $request){
        try {
            $nfce = PdvVendaService::salvarNfcePelaVenda($request->all());
            echo json_encode($nfce);
        } catch (\Exception $e) {
            i($e->getMessage());
        }
    }
   
    public function transmitirPelaVenda($id_venda){
        $nfce = Nfce::where("venda_id",$id_venda)->first();
        if(!$nfce){
            echo json_encode("-1");
            exit;
        }        
        return $this->transmitirNfce($nfce);
    }
    
    
   
    //Imprim
    public function imprimirNfcePelaVenda($id_venda){
        $venda       = PdvVenda::find($id_venda);
        $nfce        = Nfce::where("venda_id", $venda->id)->first();
        $danfce      = NfceService::danfce($nfce->chave);
        if(!$danfce->tem_erro){
            return response($danfce->pdf)->header("Content-type", "application/pdf");
        }else{
            return response()->json("erro",401);
        }
    } 
    
    public function imprimirNfcePeloNfce($id_nfce){
        $nfce        = Nfce::where("id", $id_nfce)->first();
        $danfce      = NfceService::danfce($nfce->chave);
        if(!$danfce->tem_erro){
            return response($danfce->pdf)->header("Content-type", "application/pdf");
        }else{
            return response()->json("erro",401);
        }
    }
    
    public function imprimirNfcePelaChave($chave){
        $danfce      = NfceService::danfce($chave);
        if(!$danfce->tem_erro){
            return response($danfce->pdf)->header("Content-type", "application/pdf");
        }else{
            return response()->json("erro",401);
        }
    }*/
    
}
