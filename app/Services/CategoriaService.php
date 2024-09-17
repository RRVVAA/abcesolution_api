<?php
namespace App\Services;

use App\Repositorios\Contratos\CategoriaRepositorioInterface;
use App\Repositorios\Contratos\EmpresaRepositorioInterface;

class CategoriaService
{
    protected $empresaRepositorio;
    protected $categoriaRepositorio;
    
    public function __construct(CategoriaRepositorioInterface $categoriaRepositorio, 
                                EmpresaRepositorioInterface $empresaRepositorio) {
        $this->categoriaRepositorio = $categoriaRepositorio;
        $this->empresaRepositorio = $empresaRepositorio;
        
    }        
    
    public function getCategoriaPorEmpresaUuid(string $uuid){
        $empresa = $this->empresaRepositorio->getEmpresaPorUuid($uuid);
        return $this->categoriaRepositorio->getCategoriasPorEmpresaId($empresa->id);
    }
    
    public function getCategoriaPorUuid(string $uuid){
        return $this->categoriaRepositorio->getCategoriaPorUuid($uuid);
    }
}

