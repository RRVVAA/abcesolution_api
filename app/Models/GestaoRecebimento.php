<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GestaoRecebimento extends Model{
    use HasFactory;
    protected $fillable = [
        "descricao_recebimento",
        "tipo_documento",
        "documento_id",
        "forma_pagto_id",
        "data_recebimento",
        "numero_documento",
        "valor_original",
        "valor_recebido",
        "juros",
        "multa",
        "observacao",
        "desconto"
    ];
    
    
    public function forma_pagto(){
        return $this->belongsTo(FormaPagto::class,"forma_pagto_id","id");
    }
    
    public static function filtroPorData($data1, $data2){
        $retorno = self::whereBetween('data_recebimento',[$data1, $data2]);
        return $retorno;
    }
    
    public static function filtroPorMes($mes, $ano){
        $retorno = self::whereYear('data_recebimento', '=', $ano)
                        ->whereMonth('data_recebimento', '=', $mes)
                        ->get();;
        return $retorno;
    }
}
