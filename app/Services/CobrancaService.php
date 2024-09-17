<?php
namespace App\Services;


use App\Models\Cliente;
use App\Models\Cobranca;

class CobrancaService
{  
    public static function lista($identificador){
        $cliente = Cliente::where("uuid", $identificador)->first();
        return Cobranca::where("cliente_id", $cliente->id)->get();       
    }
    
    public static function detalhe($uuid){
        return Cobranca::where(["uuid"=>$uuid])->first();
    }  
       
   
}

