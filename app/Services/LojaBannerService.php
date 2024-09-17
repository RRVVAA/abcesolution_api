<?php
namespace App\Services;

use App\Repositorios\Contratos\EmpresaRepositorioInterface;
use App\Repositorios\Contratos\LojaBannerRepositorioInterface;

class LojaBannerService
{
    protected $empresaRepositorio;
    protected $lojaBannerRepositorio;
    
    public function __construct(LojaBannerRepositorioInterface $lojaBannerRepositorio, 
                                EmpresaRepositorioInterface $empresaRepositorio) {
        $this->lojaBannerRepositorio = $lojaBannerRepositorio;
        $this->empresaRepositorio = $empresaRepositorio;
        
    }        
    
    public function listaLojaBannerPorEmpresaUuid(string $uuid){
        $empresa = $this->empresaRepositorio->getEmpresaPorUuid($uuid);
        return $this->lojaBannerRepositorio->getLojaBannerPorEmpresaId($empresa->id);
    }    
    
}

