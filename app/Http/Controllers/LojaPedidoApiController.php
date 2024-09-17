<?php

namespace App\Http\Controllers;

use App\Http\Resources\LojaPedidoResource;
use App\Services\LojaPedidoService;
use Illuminate\Http\Request;
use App\Services\EmpresaService;

class LojaPedidoApiController extends Controller
{
    protected $lojaPedidoService;
    protected $empresaService;
    public function __construct(LojaPedidoService $lojaPedidoService, EmpresaService $empresaService){
        $this->lojaPedidoService = $lojaPedidoService;
        $this->empresaService = $empresaService;
    }
    
    public function store(Request $request){
        $empresa = $this->empresaService->getEmpresaPorUuid($request->empresa_uuid);
        $pedido = $this->lojaPedidoService->criarNovoPedido($request->all(), $empresa);
        return new LojaPedidoResource($pedido);
    }   
    
    public function finalizar($pedido_id){
        $retorno = $this->lojaPedidoService->finalizar($pedido_id);
        return response()->json($retorno);
        
    } 
   
        
}

