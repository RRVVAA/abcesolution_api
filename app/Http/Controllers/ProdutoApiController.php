<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProdutoRequest;
use App\Http\Resources\ProdutoResource;
use App\Services\ProdutoService;

class ProdutoApiController extends Controller
{
    protected $produtoService;
    public function __construct(ProdutoService $produtoService){
        $this->produtoService = $produtoService;
    }   
        
    
    public function produtosPorEmpresa($token){
        $lista =  $this->produtoService->getProdutoPorEmpresaUuid($token);     
        if(!$lista){
            $retorno = new \stdClass();
            $retorno->tem_erro = true;
            $retorno->erro = "Produto Não Encontrado";
            return response()->json(["data" =>$retorno ]);
        }
     
        return ProdutoResource::collection($lista);
    }    
    
    public function pesquisaPorNome($nome, $token){        
        $lista = $this->produtoService->pesquisaPorNome($nome, $token);       
        if(count($lista) <=0){
            $retorno = new \stdClass();
            $retorno->tem_erro = true;
            $retorno->erro = "Produto Não Encontrado";
            return response()->json(["data" =>$retorno ]);
        }        
        return ProdutoResource::collection($lista);
    }    
   
    public function produtoPorCodigo($codigo, $token){
        $produto = $this->produtoService->pesquisaPorCodigo($codigo, $token);
        if(!$produto){
            $retorno = new \stdClass();
            $retorno->tem_erro = true;
            $retorno->erro = "Produto Não Encontrado";
            return response()->json(["data" =>$retorno ]);
        }
        return new ProdutoResource($produto);
    }
    
    public function produtoPorCodigoBarra($barra, $token){
        $produto =  $this->produtoService->pesquisaPorCodigoBarra($barra, $token);
        if(!$produto){
            $retorno = new \stdClass();
            $retorno->tem_erro = true;
            $retorno->erro = "Produto Não Encontrado";
            return response()->json(["data" =>$retorno ]);
        }
        return new ProdutoResource($produto);
    }
    
    public function produtoPorIdOuCodigoBarra($valor, $token){
        $produto =  $this->produtoService->produtoPorIdOuCodigoBarra($valor, $token);
        if(!$produto){
            $retorno = new \stdClass();
            $retorno->tem_erro = true;
            $retorno->erro = "Produto Não Encontrado";
            return response()->json(["data" =>$retorno ]);
        }
        return new ProdutoResource($produto);
    }
    
    public function show(ProdutoRequest $request, $uuid){
        $produto= $this->produtoService->getProdutoPorUuid($uuid);
        if(!$produto){
            $retorno = new \stdClass();
            $retorno->tem_erro = true;
            $retorno->erro = "Produto Não Encontrado";
            return response()->json(["data" =>$retorno ]);
        }
        return new ProdutoResource($produto);
    }
}
