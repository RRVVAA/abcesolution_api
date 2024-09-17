<?php

namespace App\Http\Controllers;

use App\Services\LojaService;
use Illuminate\Http\Request;
use App\Services\CupomDescontoService;

class LojaApiController extends Controller
{    
    
    public function home(Request $request){
        $dados = (object) $request->all();
        $resultado = LojaService::mostrarNaloja($dados);       
        return response()->json(["data" =>$resultado], 404);        
    }
    
    public function novoCliente(Request $request){
        $dados = (object) $request->all();
        $resultado = LojaService::novoCliente($dados);
        return response()->json(["data" =>$resultado], 404);
    }
    
    public function retormarPedidoDaLojaPeloPdv(Request $request){
        $dados = (object) $request->all();
        $resultado = LojaService::retormarPedidoDaLojaPeloPdv($dados);
        return response()->json(["data" =>$resultado]);
    }
    
    public function getPedidoPeloUuid($uuid){
        $resultado = LojaService::getPedidoPeloUuid($uuid);
        return response()->json(["data" =>$resultado], 404);
    }
    
    public function getEnderecoPeloId($endereco_id){
        $resultado = LojaService::getEnderecoPeloId($endereco_id);
        return response()->json(["data" =>$resultado], 404);
    }
    
    
    public function salvarEnderecoCliente(Request $request){
        $dados = (object) $request->all();
        $resultado = LojaService::salvarEnderecoCliente($dados);
        return response()->json(["data" =>$resultado], 404);
    }
    
    public function finalizarPedido(Request $request){
        $dados = (object) $request->all();
        $resultado = LojaService::finalizarPedido($dados);
        return response()->json(["data" =>$resultado], 404);
    }
    
    public function atualizarItem(Request $request){
        $dados = (object) $request->all();
        $resultado = LojaService::atualizarItem($dados);
        return response()->json(["data" =>$resultado], 404);
    }
    
    public function excluirItem(Request $request){
        $dados = (object) $request->all();
        $resultado = LojaService::excluirItem($dados);
        return response()->json(["data" =>$resultado], 404);
    }
    
    public function novoPedido(Request $request){
        $dados = (object) $request->all();
        $resultado = LojaService::novoPedido($dados);
        return response()->json(["data" =>$resultado], 404);
    }
    
    public function addItem(Request $request){
        $dados = (object) $request->all();
        $resultado = LojaService::addItem($dados);
        return response()->json(["data" =>$resultado], 404);
    }
    
    public function aplicarCupom(Request $request){
        $dados      = (object) $request->all();    
        $resultado  = CupomDescontoService::aplicarCupom($dados);
        return response()->json(["data" =>$resultado]);
    }
    
    public function excluirCupom($pedido_id){
        $resultado  = CupomDescontoService::excluirCupom($pedido_id);
        return response()->json(["data" =>$resultado]);
    }
    
    
    public function categoria($token, $categoria_id){
        $resultado = LojaService::categoria($token, $categoria_id);
        return response()->json(["data" =>$resultado], 404);
    }
    
    public function subcategoria($token, $subcategoria_id){
        $resultado = LojaService::subcategoria($token, $subcategoria_id);
        return response()->json(["data" =>$resultado], 404);
    }
    
    public function pesquisa($token, $q){
        $resultado = LojaService::pesquisa($token, $q);
        return response()->json(["data" =>$resultado], 404);
    }
    
    
    
    
    
    
    public function detalhe($uuid){ 
        $resultado = LojaService::detalhe($uuid);        
        return response()->json(["data" =>$resultado], 404);
    }
    
    public function carrinho(Request $request){
      
        $dados = (object) $request->all();    
        $resultado = LojaService::carrinho($dados);
        return response()->json(["data" =>$resultado], 404);
    }
    
    public function perfil(Request $request){
        $dados = (object) $request->all();
        $resultado = LojaService::perfil($dados);
        return response()->json(["data" =>$resultado], 404);
    }
    
    
    
   
    
    
    
    
    
    public function setarEnderecoFrete(Request $request){
        $dados = (object) $request->all();
        $resultado = LojaService::setarEnderecoFrete($dados);
        return response()->json(["data" =>$resultado], 404);
    }
    
    public function logar(Request $request){
        $dados = (object) $request->all();
        $resultado = LojaService::logar($dados);
        return response()->json(["data" =>$resultado], 404);
    }
}
