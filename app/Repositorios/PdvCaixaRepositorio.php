<?php
namespace App\Repositorios;

use App\Models\PdvCaixa;
use App\Repositorios\Contratos\PdvCaixaRepositorioInterface;
use Illuminate\Support\Facades\DB;
use App\Models\PdvDuplicata;

class PdvCaixaRepositorio implements PdvCaixaRepositorioInterface
{
    protected $entidade;
    
    
    public function __construct(PdvCaixa $pdvCaixa){
        $this->entidade = $pdvCaixa;  
    }
    
    public function listaCaixaAbertoPorUsuario($id){
        return $this->entidade->where("usuario_abriu_id", $id)
                              ->where("status_id", config("constantes.status.ABERTO"))
                              ->get();
    }  
     
    public function verificaSeTemCaixaAbertoPorUsuario($id){
        return $this->entidade->where("usuario_abriu_id", $id)
               ->where("status_id", config("constantes.status.ABERTO"))
               ->first();
    }
    public function abrir(array $dados){
        return $this->entidade->create($dados);
    }
    
    public function getCaixa($caixa_id){
        return $this->entidade->where("id", $caixa_id)->first();
    }
    
    public function atualizar($caixa_id){
        $caixa              = $this->entidade->find($caixa_id);
        
        //Calcula o total
        $total_venda        = DB::table('pdv_vendas')->where("caixa_id", $caixa->id)->sum("valor_liquido");
        $total_sangria      = DB::table('pdv_sangrias')->where("caixa_id", $caixa->id)->sum("valor");
        $total_suplemento   = DB::table('pdv_suplementos')->where("caixa_id", $caixa->id)->sum("valor");
        $pagamento_dinheiro = PdvDuplicata::where(["caixa_id" => $caixa->id, "tPag" =>config("constantes.forma_pagto.DINHEIRO")])->sum("vDup");
        
        $caixa->valor_vendido       = $total_venda;
        $caixa->valor_sangria       = $total_sangria;
        $caixa->valor_suplemento    = $total_suplemento;
        $caixa->dinheiro_gaveta     = $caixa->valor_abertura - $caixa->valor_sangria + $caixa->valor_suplemento + $pagamento_dinheiro ;        
        $caixa->total_em_caixa      = $caixa->valor_abertura + $caixa->valor_vendido - $caixa->valor_sangria + $caixa->valor_suplemento;
        $caixa->save();
        
    }

}

