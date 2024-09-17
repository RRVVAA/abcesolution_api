<?php

namespace App\Http\Controllers;

use App\Http\Resources\PdvVendaResource;
use App\Services\ImportacaoParaVendaPdvService;
use App\Services\ResgateService;
use Illuminate\Http\Request;

class PdvResgateApiController extends Controller
{
 
    public function lista($empresa_uuid){
        $lista = ResgateService::lista($empresa_uuid);
        return response()->json($lista, 200);
    }
    
    public function resgatar(Request $request){       
        $retorno = null;
        if($request->plataforma=="balcao"){
            $retorno = ResgateService::buscaBalcao($request->codigo);
        }else if($request->plataforma=="loja"){
            $retorno = ResgateService::buscaLoja($request->codigo);
        }else if($request->plataforma=="orcamento"){
            $retorno = ResgateService::buscaOrcamento($request->codigo);
        }else if($request->plataforma=="os"){
            $retorno = ResgateService::buscaOs($request->codigo);
        }else if($request->plataforma=="pedido"){
            $retorno = ResgateService::buscaPedido($request->codigo);
        }else if($request->plataforma=="cobranca"){
            $retorno = ResgateService::buscaCobranca($request->codigo);
        }else if($request->plataforma=="venda"){
            $retorno = ResgateService::buscaVenda($request->codigo);
        } 
        return response()->json($retorno, 200);
        
    }
    
    public function criarPdvVendaPeloBalcao(Request $request){
        $dados = (object) $request->all();
        $retorno = ImportacaoParaVendaPdvService::criarPdvVendaPeloBalcao($dados);
        return new PdvVendaResource($retorno);
    }
    
    public function criarPdvVendaPelaLoja(Request $request){
        $dados = (object) $request->all();
        $retorno = ImportacaoParaVendaPdvService::criarPdvVendaPelaLoja($dados);    
        return new PdvVendaResource($retorno);
    }
    
    public function criarPdvVendaPeloOrcamento(Request $request){
        $dados = (object) $request->all();
        $retorno = ImportacaoParaVendaPdvService::criarPdvVendaPeloOrcamento($dados);
        return new PdvVendaResource($retorno);
    }
    
    public function criarPdvVendaPeloPedidoCliente(Request $request){
        $dados = (object) $request->all();
        $retorno = ImportacaoParaVendaPdvService::criarPdvVendaPeloPedidoCliente($dados);
        return new PdvVendaResource($retorno);
    }
    
    public function criarPdvVendaPelVenda(Request $request){
        $dados = (object) $request->all();
        $retorno = ImportacaoParaVendaPdvService::criarPdvVendaPelVenda($dados);
        return new PdvVendaResource($retorno);
    }
    
    public function criarPdvVendaPelOrdemServico(Request $request){
        $dados = (object) $request->all();
        $retorno = ImportacaoParaVendaPdvService::criarPdvVendaPelOrdemServico($dados);
        return new PdvVendaResource($retorno);
    }
}
