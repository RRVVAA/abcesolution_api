<?php
namespace App\Repositorios;

use App\Models\Produto;
use App\Repositorios\Contratos\ProdutoRepositorioInterface;

class ProdutoRepositorio implements ProdutoRepositorioInterface
{
    protected $entidade;
    
    
    public function __construct(Produto $produto){
          $this->entidade = $produto;  
    }
    public function produtosPorEmpresaUuid($uuid_empresa) {
        return $this->entidade->join("empresas","empresas.id", "*", "produtos.empresa_id")
        ->where("empresas.uuid", $uuid_empresa)
                ->select("produtos.*")
                ->get();
    }    
    
    public function pesquisaPorNome($nome, $empresa_id){
       return $this->entidade->where("nome","like","%$nome%")->where("empresa_id", $empresa_id)->get();
    }
    
    public function getProdutosPorEmpresaId($id_empresa){
        return $this->entidade->where("empresa_id", $id_empresa)->get();
    }    
    public function getProdutoPorUuid($uuid){
        return $this->entidade->where("uuid", $uuid)->first();
    }
    public function pesquisaPorCodigo($codigo, $empresa_id)
    {
        return $this->entidade->where("id",$codigo)->where("empresa_id", $empresa_id)->first();
    }
    public function pesquisaPorCodigoBarra($barra, $empresa_id)
    {
        return $this->entidade->where("codigo_barra",$barra)->where("empresa_id", $empresa_id)->first();
    }




    
}

