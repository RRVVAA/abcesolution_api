<?php
namespace App\Services;

use App\Models\FormaPagto;
use App\Models\PdvPagamento;
use App\Models\PdvCaixa;
use App\Models\PdvSangria;
use App\Models\PdvSuplemento;

class CaixaService{
    public static function listaSomaPorFormaPagto($id_caixa){        
        $formas = FormaPagto::whereIn('id', [1,3,4,17])->get() ;
        foreach($formas as $f){
            $soma = PdvPagamento::where("forma_pagto_id", $f->id)->where("caixa_id", $id_caixa)->sum("valor");
            $f->total = $soma ? $soma: 0 ;
        }  
		
        return $formas;
    }
    
    public static function valores($id_caixa){
        $retorno = new \stdClass();
        $caixa                = PdvCaixa::find($id_caixa);
        $retorno->faturamento = PdvPagamento::where("caixa_id", $id_caixa)->sum("valor");
        $retorno->sangria     = PdvSangria::where("caixa_id", $id_caixa)->sum("valor");
        $retorno->suplemento  = PdvSuplemento::where("caixa_id", $id_caixa)->sum("valor"); 
        $retorno->troco       = $caixa->valor_abertura;
        $retorno->total       = $retorno->faturamento - $retorno->sangria + $retorno->suplemento + $retorno->troco;        
        return $retorno;
    }
}

