<?php

namespace App\Http\Controllers;

use App\Services\LojaPedidoService;
use Illuminate\Http\Request;

class TesteController extends Controller{
    public function testeapi(){
        return response()->json("OK");
    }
    
    public function vendapix(Request $request){
        $dados = $request->all();	
        try{
            LojaPedidoService::pagarPedido($dados);
		}catch (\Exception $e){           
			return response()->json(["msg"=>$e->getMessage()], 422);
        }
    }
	
    
    
}
