<?php
namespace App\Repositorios;

use App\Models\Categoria;
use App\Repositorios\Contratos\CategoriaRepositorioInterface;

class CategoriaRepositorio implements CategoriaRepositorioInterface
{
    protected $entidade;
    
    
    public function __construct(Categoria $categoria){
          $this->entidade = $categoria;  
    }
    public function categoriasPorEmpresaUuid($uuid_empresa)
    {
        return $this->entidade->join("empresas","empresas.id", "*", "categorias.empresa_id")
        ->where("empresas.uuid", $uuid_empresa)
                ->select("categorias.*")
                ->get();
    }    
    
    public function getCategoriasPorEmpresaId($id_empresa)
    {
        return $this->entidade->where("empresa_id", $id_empresa)->get();
    }
    
    public function getCategoriaPorUuid($uuid){
        return $this->entidade->where("uuid", $uuid)->first();
    }



    
}

