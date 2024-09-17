<?php
namespace App\Repositorios;

use App\Models\PdvSuplemento;
use App\Repositorios\Contratos\PdvSuplementoRepositorioInterface;

class PdvSuplementoRepositorio implements PdvSuplementoRepositorioInterface
{
    protected $entidade;
    
    
    public function __construct(PdvSuplemento $pdvSuplemento){
        $this->entidade = $pdvSuplemento;  
    }
    public function salvar($dados, $usuario)
    {
        $d = [
            "caixa_id"   => $dados->caixa_id,
            "empresa_id" => $usuario->empresa_id,
            "usuario_id" => $usuario->id ,
            "descricao"  => $dados->descricao,
            "valor"      => $dados->valor,
        ];        
        $suplemento = $this->entidade->create($d);
      /*  if($suplemento){
            MovimentoContaService::inserirMovimentoSuplemento($suplemento);
        }*/
        
        return $suplemento;
    }

    public function listaPorCaixa($caixa_id){
        return $this->entidade->where("caixa_id", $caixa_id)->get();
    }
    
    public function listaPorUsuario($usuario_id){
        return $this->entidade->where("usuario_id", $usuario_id)->get();
    }
   
     
   

}

