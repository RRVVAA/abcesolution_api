<?php
namespace App\Services;

use App\Repositorios\Contratos\EmpresaRepositorioInterface;
use App\Repositorios\Contratos\PdvNumeroRepositorioInterface;
use App\Repositorios\Contratos\UsuarioRepositorioInterface;

class PdvNumeroService
{    
    protected $usuarioRepositorio;
    protected $empresaRepositorio;
    protected $pdvNumeroRepositorio;
    
    
    public function __construct(PdvNumeroRepositorioInterface $pdvNumeroRepositorio,
                                EmpresaRepositorioInterface $empresaRepositorio,
                                UsuarioRepositorioInterface $usuarioRepositorio) {
        $this->usuarioRepositorio   = $usuarioRepositorio; 
        $this->empresaRepositorio   = $empresaRepositorio; 
        $this->pdvNumeroRepositorio  = $pdvNumeroRepositorio;      
    }       
    
    public function listaPdvNumeroPorEmpresa(string $token){
        $empresa = $this->empresaRepositorio->getEmpresaPorUuid($token);
        return $this->pdvNumeroRepositorio->listaPdvNumeroPorEmpresa($empresa->id);
    }
    
    
}

