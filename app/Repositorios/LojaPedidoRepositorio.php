<?php
namespace App\Repositorios;

use App\Models\LojaBanner;
use App\Repositorios\Contratos\LojaPedidoRepositorioInterface;

class LojaPedidoRepositorio implements LojaPedidoRepositorioInterface
{
    protected $entidade;
    
    
    public function __construct(LojaBanner $lojaBanner){
        $this->entidade = $lojaBanner;  
    }   
    
    public function criarNovoPedido($dados)
    {
        echo "veio";
        i($dados);
    }

  
    
}

