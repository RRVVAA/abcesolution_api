<?php

namespace App\Http\Controllers;

use App\Http\Resources\PdvSangriaResource;
use App\Services\PdvSangriaService;
use Illuminate\Http\Request;

class PdvSangriaApiController extends Controller
{
    protected $pdvSangriaService;
    public function __construct(PdvSangriaService $pdvSangriaService){
        $this->pdvSangriaService = $pdvSangriaService;
    }    
        
    public function salvar(Request $request){
        $dados = (object) $request->all();
        $retorno = $this->pdvSangriaService->salvar($dados);        
        if(!$retorno){
            return response()->json(["data" =>"", "message" => "Erro a inserir Sangria"], 404);
        }
        return new PdvSangriaResource($retorno);        
    } 
    
    public function listaPorCaixa($caixa_id){
        $retorno = $this->pdvSangriaService->listaPorCaixa($caixa_id);
        return PdvSangriaResource::collection($retorno);
    }
    
    public function listaPorUsuario($usuario_uuid){
        $retorno = $this->pdvSangriaService->listaPorUsuario($usuario_uuid);
        return PdvSangriaResource::collection($retorno);
    }
    
}
