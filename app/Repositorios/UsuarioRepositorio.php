<?php
namespace App\Repositorios;

use App\Models\User;
use App\Repositorios\Contratos\UsuarioRepositorioInterface;

class UsuarioRepositorio implements UsuarioRepositorioInterface
{
    protected $entidade;
    
    public function __construct(User $usuario){
        $this->entidade = $usuario;  
    }    
    
    public function getUsuarioPorUuid($uuid)
    {
       return $this->entidade->where("uuid", $uuid)->first();
    }


    
}

