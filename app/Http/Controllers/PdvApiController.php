<?php

namespace App\Http\Controllers;

use App\Http\Resources\LoginResource;
use App\Http\Resources\PdvVendaResource;
use App\Services\ClienteService;
use App\Services\CupomDescontoService;
use App\Services\GradeService;
use App\Services\PdvService;
use App\Services\PdvVendaService;
use Illuminate\Http\Request;

class PdvApiController extends Controller
{      
    
    public function home(Request $request){
        $dados = (object) $request->all();
        $resultado = PdvService::mostrarNoPdv($dados);
        return response()->json(["data" =>$resultado], 404);
    }
    
    public function abrirCaixa(Request $request){
        $dados = (object) $request->all();        
        $usuario = PdvService::abrirCaixa($dados);
        return new LoginResource($usuario);
    }
    
    public function fecharCaixa(Request $request){
        $dados = (object) $request->all();
        $usuario = PdvService::fecharCaixa($dados);
        return new LoginResource($usuario);
    }
    
    public function aplicarCupom(Request $request){
        $dados      = (object) $request->all();
        $resultado  = PdvVendaService::aplicarCupom($dados);       
        return response()->json(["data" =>$resultado]);
    }
    
    public function excluirCupom(Request $request){
        $dados      = (object) $request->all();
        $resultado  = PdvVendaService::excluirCupom($dados);
        return response()->json(["data" =>$resultado]);
    }
    public function inserirItem(Request $request){
        $dados = (object) $request->all();
        $resultado = PdvService::inserirItem($dados);        
        $retorno = new \stdClass();
        if($resultado->eh_grade){
            if($resultado->produto_id!=null){
                $retorno->eh_grade = true;
                $retorno->grade    = GradeService::montar($resultado->produto_id);
                return response()->json(["data" =>$retorno]);
            }else{
                $retorno = new \stdClass();
                $retorno->id        = null;
                $retorno->tem_erro  = true;
                $retorno->erro      = "O estoque deste produto está diferente do somatório da grade, regularize o estoque para poder dar saída no produto";
                return response()->json(["data" =>$retorno ]);
            }
            
        }else{
            if($resultado->venda_id){
                $retorno = PdvVendaService::getVendaPorId($resultado->venda_id);
                return new PdvVendaResource($retorno);
            }else{
                $retorno = new \stdClass();
                $retorno->id        = null;
                $retorno->tem_erro  = true;
                $retorno->erro      = "Produto Não Encontrado";
                return response()->json(["data" =>$retorno ]);
            }
        }
        
        return response()->json(["data" =>$retorno]);
    }
    
    public function buscaCliente(Request $request){
        $dados = (object) $request->all();
        $lista = ClienteService::buscaCliente($dados);
        return response()->json(["data" =>$lista ]);
    }
    
    public function vincularCliente(Request $request){
        $dados = (object) $request->all();
        $venda = PdvService::vincularCliente($dados);        
        return new PdvVendaResource($venda);
    }
    
    public function gerarCrediario(Request $request){
        $dados = (object) $request->all();
        $venda = PdvService::gerarCrediario($dados);
        return new PdvVendaResource($venda);
    }
    
    public function gerarPagamentoCartao(Request $request){
        $dados = (object) $request->all();
        $venda = PdvService::gerarPagamentoCartao($dados);
        return new PdvVendaResource($venda);
    }
    
    public function armazenarVenda(Request $request){
        $dados = (object) $request->all();
        $venda = PdvService::armazenarVenda($dados);
        return response()->json(["data" =>$venda ]);
    }
}
