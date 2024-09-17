<?php
namespace App\Services;

use App\Repositorios\Contratos\PdvCaixaRepositorioInterface;
use App\Repositorios\Contratos\PdvSangriaRepositorioInterface;
use App\Repositorios\Contratos\UsuarioRepositorioInterface;

class PdvSangriaService
{    
    protected $usuarioRepositorio;
    protected $pdvSangriaRepositorio;
    protected $pdvCaixaRepositorio;
    
    
    public function __construct(PdvSangriaRepositorioInterface $pdvSangriaRepositorio, PdvCaixaRepositorioInterface $pdvCaixaRepositorio,
                                UsuarioRepositorioInterface $usuarioRepositorio) {
        $this->usuarioRepositorio     = $usuarioRepositorio; 
        $this->pdvSangriaRepositorio  = $pdvSangriaRepositorio; 
        $this->pdvCaixaRepositorio = $pdvCaixaRepositorio;
    }       
    
    public function salvar($dados){
        $usuario = $this->usuarioRepositorio->getUsuarioPorUuid($dados->usuario_uuid);        
        $sangria =  $this->pdvSangriaRepositorio->salvar($dados, $usuario);
        $this->pdvCaixaRepositorio->atualizar($sangria->caixa_id);        
        return $sangria;
    }
    
    public function listaPorCaixa($caixa_id){
        return $this->pdvSangriaRepositorio->listaPorCaixa($caixa_id);
    }
    
    public function listaPorUsuario($usuario_uuid){
        $usuario = $this->usuarioRepositorio->getUsuarioPorUuid($usuario_uuid);  
        return $this->pdvSangriaRepositorio->listaPorUsuario($usuario->id);
    }
    
    
}

