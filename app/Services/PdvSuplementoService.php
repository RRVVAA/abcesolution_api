<?php
namespace App\Services;

use App\Repositorios\Contratos\PdvCaixaRepositorioInterface;
use App\Repositorios\Contratos\PdvSuplementoRepositorioInterface;
use App\Repositorios\Contratos\UsuarioRepositorioInterface;

class PdvSuplementoService
{    
    protected $usuarioRepositorio;
    protected $pdvSuplementoRepositorio;
    protected $pdvCaixaRepositorio;
    
    
    public function __construct(PdvSuplementoRepositorioInterface $pdvSuplementoRepositorio, PdvCaixaRepositorioInterface $pdvCaixaRepositorio,
                                UsuarioRepositorioInterface $usuarioRepositorio) {
        $this->usuarioRepositorio     = $usuarioRepositorio; 
        $this->pdvSuplementoRepositorio  = $pdvSuplementoRepositorio;
        $this->pdvCaixaRepositorio = $pdvCaixaRepositorio;
    }       
    
    public function salvar($dados){
        $usuario = $this->usuarioRepositorio->getUsuarioPorUuid($dados->usuario_uuid);
        $suplemento =  $this->pdvSuplementoRepositorio->salvar($dados, $usuario);
        $this->pdvCaixaRepositorio->atualizar($suplemento->caixa_id);
        return $suplemento;
    }
    
    public function listaPorCaixa($caixa_id){
        return $this->pdvSuplementoRepositorio->listaPorCaixa($caixa_id);
    }
    
    public function listaPorUsuario($usuario_uuid){
        $usuario = $this->usuarioRepositorio->getUsuarioPorUuid($usuario_uuid);
        return $this->pdvSuplementoRepositorio->listaPorUsuario($usuario->id);
    }
}

