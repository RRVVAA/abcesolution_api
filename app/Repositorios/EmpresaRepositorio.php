<?php
namespace App\Repositorios;

use App\Repositorios\Contratos\EmpresaRepositorioInterface;
use App\Models\Empresa;

class EmpresaRepositorio implements EmpresaRepositorioInterface
{
    protected $entidade;
    
    public function __construct(Empresa $empresa){
          $this->entidade = $empresa;  
    }
    public function getAllEmpresas()
    {
        return $this->entidade->all();
    }
    
    public function getEmpresaPorUuid($uuid)
    {
       return $this->entidade->where("uuid", $uuid)->first();
    }


    
}

