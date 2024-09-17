<?php
namespace App\Services;

use App\Repositorios\Contratos\EmpresaRepositorioInterface;

class EmpresaService
{
    private $repositorio;
    
    public function __construct(EmpresaRepositorioInterface $repositorio) {
        $this->repositorio = $repositorio;
    }
    
    public function getAllEmpresas(){
        return $this->repositorio->getAllEmpresas();
    }
    
    public function getEmpresaPorUuid($uuid){
        return $this->repositorio->getEmpresaPorUuid($uuid);
    }
}

