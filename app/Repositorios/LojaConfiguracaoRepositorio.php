<?php
namespace App\Repositorios;

use App\Models\LojaConfiguracao;
use App\Repositorios\Contratos\LojaConfiguracaoRepositorioInterface;

class LojaConfiguracaoRepositorio implements LojaConfiguracaoRepositorioInterface
{
    protected $entidade;
    
    
    public function __construct(LojaConfiguracao $lojaConfiguracao){
        $this->entidade = $lojaConfiguracao;  
    }     
    
    public function getLojaConfiguracaoPorEmpresaId($id_empresa)
    {
        return $this->entidade->where("empresa_id", $id_empresa)->first();
    }
  
    
}

