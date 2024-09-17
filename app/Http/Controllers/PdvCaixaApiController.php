<?php

namespace App\Http\Controllers;

use App\Http\Resources\PdvCaixaResource;
use App\Models\PdvVenda;
use App\Services\PdvCaixaService;
use Illuminate\Http\Request;

class PdvCaixaApiController extends Controller
{
    protected $pdvCaixaService;
    public function __construct(PdvCaixaService $pdvCaixaService){
        $this->pdvCaixaService = $pdvCaixaService;
    }       
    
    public function getCaixa($caixa_id){
        $retorno = $this->pdvCaixaService->getCaixa($caixa_id);
        if(!$retorno){
            return response()->json(["data" =>"", "erro" => "Nenhum Caixa Encontrada"], 404);
        }
        return new PdvCaixaResource($retorno);
    }
    
    public function detalhamento($caixa_id){
        $caixa         = new \stdClass();
        $caixa->caixa  = $this->pdvCaixaService->getCaixa($caixa_id);
        $caixa->formas = $this->pdvCaixaService->listaSomaPorFormaPagto($caixa_id);
        $caixa->valores= $this->pdvCaixaService->valores($caixa_id);
        $caixa->vendas = PdvVenda::where("caixa_id",$caixa_id)->get();
        $caixa->pode_fechar = $this->pdvCaixaService->podeFechar($caixa_id);
        return response()->json($caixa);
    }
    
    public function listaCaixaAbertoPorUsuario($uuid){
        $retorno = $this->pdvCaixaService->listaCaixaAbertoPorUsuario($uuid);        
        return PdvCaixaResource::collection($retorno);
    }    
    
    public function verificaSeTemCaixaAbertoPorUsuario($uuid){
        $caixa= $this->pdvCaixaService->verificaSeTemCaixaAbertoPorUsuario($uuid);
        if(!$caixa){
            return response()->json(["data" =>"", "erro" => "Nenhum Caixa Encontrada"], 404);
        }
        return new PdvCaixaResource($caixa);
    }
    
    public function abrir(Request $request){        
        $caixa = $this->pdvCaixaService->abrir($request->all());
        return new PdvCaixaResource($caixa);
    }
    
    public function fechar($caixa_id, $usuario_id){
        return $this->pdvCaixaService->fechar($caixa_id, $usuario_id);
    }
    
}
