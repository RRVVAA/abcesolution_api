<?php

namespace App\Http\Controllers;

use App\Services\DeliveryService;
use Illuminate\Http\Request;

class DeliveryApiController extends Controller
{    
    
    public function home(Request $request){
        $dados      = (object) $request->all();
        $resultado  = DeliveryService::mostrarNaloja($dados);
        return response()->json(["data" =>$resultado]);
    }
    
    public function novoPedido(Request $request){
        $dados = (object) $request->all();
        $resultado = DeliveryService::novoPedido($dados);
        return response()->json(["data" =>$resultado]);
    }
    
    public function addItem(Request $request){
        $dados = (object) $request->all();
        $resultado = DeliveryService::addItem($dados);
        return response()->json(["data" =>$resultado], 404);
    }
   
    public function novoCliente(Request $request){
        $dados = (object) $request->all();
        $resultado = DeliveryService::novoCliente($dados);
        return response()->json(["data" =>$resultado], 404);
    }
}
