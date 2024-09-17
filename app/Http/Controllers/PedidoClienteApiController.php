<?php

namespace App\Http\Controllers;

use App\Http\Requests\PedidoClienteRequest;
use App\Http\Resources\CobrancaResource;
use App\Http\Resources\PedidoClienteResource;
use App\Models\Cobranca;
use App\Services\PedidoClienteService;
use Illuminate\Http\Request;

class PedidoClienteApiController extends Controller
{
    protected $pedidoClienteService;
    public function __construct(PedidoClienteService  $pedidoClienteService){
        $this->pedidoClienteService = $pedidoClienteService;
    }
    
    public function store(PedidoClienteRequest $request){
        $pedido = $this->pedidoClienteService->criarNovoPedido($request->all());        
        return new PedidoClienteResource($pedido);
    }
    
    public function filtro(Request $request){
        $pedidos = $this->pedidoClienteService->filtro($request->all());        
        return response()->json(["data" =>$pedidos]);
    }
    
    
    public function show($identificador){
        $categoria= $this->pedidoClienteService->getPedidoPorIdentificador($identificador);
        if(!$categoria){
            return response()->json(["message" => "Pedido Não Encontrado"], 404);
        }
        return new PedidoClienteResource($categoria);
    }
        
    public function cobrancas($id){
        $cobrancas= Cobranca::where("venda_recorrente_id", $id)->get();
        return CobrancaResource::collection($cobrancas);
    }
    
}
