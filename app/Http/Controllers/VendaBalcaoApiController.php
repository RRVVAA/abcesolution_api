<?php

namespace App\Http\Controllers;

use App\Http\Resources\VendaBalcaoResource;
use App\Services\VendaBalcaoService;
use Illuminate\Http\Request;

class VendaBalcaoApiController extends Controller
{
    public function novoPedido(Request $request){        
        $vendaBalcao = VendaBalcaoService::gerarNovoPedido($request->all()); 
        if(!$vendaBalcao){
            $retorno = new \stdClass();
            $retorno->tem_erro = true;
            $retorno->erro = "Produto Não Encontrado";
            return response()->json(["data" =>$retorno ], 404);
        }
        return new VendaBalcaoResource($vendaBalcao);
    }
    
    public function buscarPedido($id){
        $vendaBalcao = VendaBalcaoService::buscarPedido($id);
        if(!$vendaBalcao){
            $retorno = new \stdClass();
            $retorno->tem_erro = true;
            $retorno->erro = "Produto Não Encontrado";
            return response()->json(["data" =>$retorno ], 404);
        }
        return new VendaBalcaoResource($vendaBalcao);
    }
    
    public function inserirItem(Request $request){
        $dados = (object) $request->all();
        VendaBalcaoService::inserirItem($dados);        
        $vendaBalcao = VendaBalcaoService::buscarPedido($dados->venda_balcao_id);
        if(!$vendaBalcao){
            $retorno = new \stdClass();
            $retorno->tem_erro = true;
            $retorno->erro = "Produto Não Encontrado";
            return response()->json(["data" =>$retorno ], 404);
        }
        return new VendaBalcaoResource($vendaBalcao);
    }
   
    public function finalizarPedido(Request $request){
        $dados = (object) $request->all();
        $vendaBalcao = VendaBalcaoService::finalizarPedido($dados->venda_id);
        return response()->json(["data" =>$vendaBalcao], 404);
    }
    
    public function cancelarPedido(Request $request){
        $dados = (object) $request->all();
        VendaBalcaoService::cancelarPedido($dados->venda_id);
        return response()->json(["data" =>"ok"]);
    }
    
    public function excluirBalcao(Request $request){
        $dados = (object) $request->all();
        VendaBalcaoService::excluirBalcao($dados);
        return response()->json(["data" =>"ok"]);
    }
    public function excluirItem(Request $request){
        $dados = (object) $request->all();
        VendaBalcaoService::excluirItem($dados);
        $vendaBalcao = VendaBalcaoService::buscarPedido($dados->venda_balcao_id);
        if(!$vendaBalcao){
            $retorno = new \stdClass();
            $retorno->tem_erro = true;
            $retorno->erro = "Produto Não Encontrado";
            return response()->json(["data" =>$retorno ], 404);
        }
        return new VendaBalcaoResource($vendaBalcao);
    }
}
