<?php
namespace App\Repositorios;

use App\Models\LojaBanner;
use App\Repositorios\Contratos\LojaBannerRepositorioInterface;

class LojaBannerRepositorio implements LojaBannerRepositorioInterface
{
    protected $entidade;
    
    
    public function __construct(LojaBanner $lojaBanner){
        $this->entidade = $lojaBanner;  
    }     
    
    public function getLojaBannerPorEmpresaId($id_empresa)
    {
        return $this->entidade->where("empresa_id", $id_empresa)->get();
    }
  
    
}

