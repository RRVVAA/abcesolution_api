<?php

namespace App\Http\Controllers;

use App\Http\Resources\PdvVendaResource;
use App\Services\PdvVendaService;
use Illuminate\Http\Request;

class PdvVendaApiController extends Controller
{
    protected $pdvVendaService;
    public function __construct(PdvVendaService $pdvVendaService){
        $this->pdvVendaService = $pdvVendaService;
    }    
    
    public function iniciarPdvVenda($uuid){
        $pdvVenda= $this->pdvVendaService->iniciarPdvVenda($uuid);        
        return response()->json(["data"=>$pdvVenda]);
        
    }
    
    public function getVendaAbertaPorUsuario($uuid, $caixa_id){
        $pdvVenda= $this->pdvVendaService->getVendaAbertaPorUsuario($uuid, $caixa_id);
        if(!$pdvVenda){
            return response()->json(["data" =>"", "erro" => "Nenhum Caixa Encontrada"], 404);
        }
        return new PdvVendaResource($pdvVenda);
    }    
         
    public function salvar(Request $request){        
        $pdvVenda = $this->pdvVendaService->salvar($request->all());        
        $retorno = $this->pdvVendaService->getVendaPorId($pdvVenda);
        return new PdvVendaResource($retorno);
    }
    
    public function finalizarVenda(Request $request){
        return $this->pdvVendaService->finalizarVenda($request->all());
    }
    
    public function cancelarVenda(Request $request){
        $dados = (object) $request->all();
        $retorno = $this->pdvVendaService->cancelarVenda($dados);
        return response()->json(["data"=>"ok"]);       
    }
    
	public function salvarItens(Request $request){        
        $pdvVenda = $this->pdvVendaService->inserirItensVenda($request->all());        
        $retorno = $this->pdvVendaService->getVendaPorId($pdvVenda);
        return new PdvVendaResource($retorno);
    }
	
    
    public function inserirItem(Request $request){
        $dados = (object) $request->all();
        $pdvVenda = $this->pdvVendaService->inserirItem($dados);
        if($pdvVenda){
            $retorno = $this->pdvVendaService->getVendaPorId($pdvVenda);
            return new PdvVendaResource($retorno);
        }else{
            $retorno = new \stdClass();
            $retorno->id        = null;
            $retorno->tem_erro  = true;
            $retorno->erro      = "Produto Não Encontrado";
            return response()->json(["data" =>$retorno ], 404);
        }
        
    }
    
    public function excluirItem($id, $idVenda){
        $this->pdvVendaService->excluirItem($id, $idVenda);
        $retorno = $this->pdvVendaService->getVendaPorId($idVenda);
        return new PdvVendaResource($retorno);
    }
    
	public function salvarPagamento(Request $request){        
        $pdvVenda = $this->pdvVendaService->inserirPagamento($request->all());        
        $retorno = $this->pdvVendaService->getVendaPorId($pdvVenda);
        return new PdvVendaResource($retorno);
    }
	
    public function enviarDescontoAcrescimento(Request $request){
        $venda_id   = $this->pdvVendaService->enviarDescontoAcrescimento($request->all());
        $retorno    = $this->pdvVendaService->getVendaPorId($venda_id);
        return new PdvVendaResource($retorno);
    }
    
    public function excluirDuplicata($id, $idVenda){
        $this->pdvVendaService->excluirDuplicata($id);
        $retorno = $this->pdvVendaService->getVendaPorId($idVenda);
        return new PdvVendaResource($retorno);
    }
    
    public function gerarNfcePelaVenda($idVenda){   
        return  $this->pdvVendaService->gerarNfcePelaVenda($idVenda);
    }
    
    public function listaPorCaixa($caixa_id){
        $retorno = $this->pdvVendaService->listaPorCaixa($caixa_id);
        return PdvVendaResource::collection($retorno);
    }
    
    public function listaPorUsuario($usuario_uuid){
        $retorno = $this->pdvVendaService->listaPorUsuario($usuario_uuid);
        return PdvVendaResource::collection($retorno);
    }    
    
   
    
    public function getVendaPorId($venda_id){        
        $retorno = $this->pdvVendaService->getVendaPorId($venda_id);       
        if(!$retorno){
            return response()->json(["data" =>"", "erro" => "Nenhum Caixa Encontrada"], 404);
        }
        return new PdvVendaResource($retorno);
    }
}
