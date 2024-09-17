<?php
namespace App\Services;

use App\Repositorios\Contratos\ProdutoRepositorioInterface;
use App\Repositorios\Contratos\EmpresaRepositorioInterface;
use App\Models\Produto;

class ProdutoService
{
    protected $empresaRepositorio;
    protected $produtoRepositorio;
    
    public function __construct(ProdutoRepositorioInterface $produtoRepositorio, 
                                EmpresaRepositorioInterface $empresaRepositorio) {
        $this->produtoRepositorio = $produtoRepositorio;
        $this->empresaRepositorio = $empresaRepositorio;        
    }        
    
    public function getProdutoPorEmpresaUuid(string $uuid){
        $empresa = $this->empresaRepositorio->getEmpresaPorUuid($uuid);
        if($empresa){
            return $this->produtoRepositorio->getProdutosPorEmpresaId($empresa->id);
        }
        return array();
    }
    
    public function pesquisaPorNome($nome, $token){
        $empresa = $this->empresaRepositorio->getEmpresaPorUuid($token);
        if($empresa){
            return $this->produtoRepositorio->pesquisaPorNome($nome, $empresa->id);
        }
        return array();
    }    
    
    
    public function pesquisaPorCodigo($codigo, $token){
        $empresa = $this->empresaRepositorio->getEmpresaPorUuid($token);
        if($empresa){
            return $this->produtoRepositorio->pesquisaPorCodigo($codigo, $empresa->id);
        }
        return false;
        
    }
    
    public function pesquisaPorCodigoBarra($barra, $token){
        $empresa = $this->empresaRepositorio->getEmpresaPorUuid($token);
        if($empresa){
            return $this->produtoRepositorio->pesquisaPorCodigoBarra($barra, $empresa->id);
        }
        return false;
    }
    
    public function produtoPorIdOuCodigoBarra($valor, $token){
        $empresa = $this->empresaRepositorio->getEmpresaPorUuid($token);
        if($empresa){
            $produto =  Produto::where(["empresa_id"=>$empresa->id, "codigo_barra"=>$valor])->first();
            if(!$produto){
                $produto =  Produto::where(["empresa_id"=>$empresa->id, "id"=>$valor])->first();
            }            
            return $produto;
        }
        return false;
    }
    
    public function getProdutoPorUuid(string $uuid){
        return $this->produtoRepositorio->getProdutoPorUuid($uuid);
    }
}

