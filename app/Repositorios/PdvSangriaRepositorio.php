<?php
namespace App\Repositorios;

use App\Models\PdvSangria;
use App\Repositorios\Contratos\PdvSangriaRepositorioInterface;
use App\Services\MovimentoContaBancariaService;

class PdvSangriaRepositorio implements PdvSangriaRepositorioInterface
{
    protected $entidade;
    
    
    public function __construct(PdvSangria $pdvSangria){
        $this->entidade = $pdvSangria;  
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
        
        $sangria = $this->entidade->create($d);
       
        if($sangria){
            MovimentoContaBancariaService::inserirMovimentoSangrira($sangria);
        }
        
        return $sangria;
    }
    
    public function listaPorCaixa($caixa_id){
        return $this->entidade->where("caixa_id", $caixa_id)->get();
    }
    
    public function listaPorUsuario($usuario_id){
        return $this->entidade->where("usuario_id", $usuario_id)->get();
    }

    


    
   
     
   

}

