<?php
namespace App\Repositorios;

use App\Repositorios\Contratos\PdvNumeroRepositorioInterface;
use App\Models\PdvCaixaNumero;

class PdvNumeroRepositorio implements PdvNumeroRepositorioInterface
{
    protected $entidade;
    
    
    public function __construct(PdvCaixaNumero $pdvNumero){
        $this->entidade = $pdvNumero;  
    }
    
    public function listaPdvNumeroPorEmpresa($id){
        return $this->entidade->where("empresa_id", $id)-> whereNotIn('id', function($query) {
            $query->select('pdv_caixas.caixanumero_id');
            $query->from('pdv_caixas');
            $query->whereRaw("pdv_caixas .status_id={config('constantes.status.ABERTO')}");
        })
        ->get();
    }  
     
   

}

