<?php

namespace App\Http\Controllers;

use App\Http\Resources\CobrancaResource;
use App\Services\CobrancaService;
use App\Services\PedidoClienteService;

class CobrancaApiController extends Controller
{    
    protected $pedidoClienteService;
    public function __construct(PedidoClienteService  $pedidoClienteService){
        $this->pedidoClienteService = $pedidoClienteService;
    }    
    
    public function lista($identificador){
        $cobrancas= CobrancaService::lista($identificador);
        return CobrancaResource::collection($cobrancas);
    }
    
    public function detalhe($uuid){
        $cobranca = CobrancaService::detalhe($uuid);
        if(!$cobranca){
            return response()->json(["data" =>"", "message" => "Erro a inserir Sangria"], 404);
        }
        return new CobrancaResource($cobranca);
    }
    
   
    
    
}
