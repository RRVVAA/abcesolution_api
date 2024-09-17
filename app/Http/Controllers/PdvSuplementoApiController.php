<?php

namespace App\Http\Controllers;

use App\Http\Resources\PdvSuplementoResource;
use App\Services\PdvSuplementoService;
use Illuminate\Http\Request;

class PdvSuplementoApiController extends Controller
{
    protected $pdvSuplementoService;
    public function __construct(PdvSuplementoService $pdvSuplementoService){
        $this->pdvSuplementoService = $pdvSuplementoService;
    }    
        
    public function salvar(Request $request){
        $dados = (object) $request->all(); 
        $retorno = $this->pdvSuplementoService->salvar($dados);
        if(!$retorno){
            return response()->json(["data" =>"", "message" => "Erro a inserir Sangria"], 404);
        }
        
        return new PdvSuplementoResource($retorno);  
    }    
    
    public function listaPorCaixa($caixa_id){
        $retorno = $this->pdvSuplementoService->listaPorCaixa($caixa_id);
        return PdvSuplementoResource::collection($retorno);
    }
    
    public function listaPorUsuario($usuario_uuid){
        $retorno = $this->pdvSuplementoService->listaPorUsuario($usuario_uuid);
        return PdvSuplementoResource::collection($retorno);
    }
}
