<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FinPagamento extends Model
{
    use HasFactory;
    protected $fillable =[
        "empresa_id",
        "usuario_id",
        "descricao_pagamento",        
        "tipo_documento",
        "documento_id",
        "forma_pagto_id",
        "data_pagamento",
        "numero_documento",
        "observacao",
        "valor_original",
        "valor_a_pagar",
        "valor_pago",
        "juros",
        "desconto",
        "multa",
        "pago"
    ];
    
    public function empresa(){
        return $this->belongsTo(Empresa::class,"empresa_id","id");
    }
    
    public function forma_pagto(){
        return $this->belongsTo(FormaPagto::class,"forma_pagto_id","id");
    }
    
    public static function filtro($filtro){
        $retorno = FinPagamento::orderBy('fin_pagamentos.data_pagamento', 'asc');
              
        if($filtro->forma_pagto_id){
            $retorno->where("forma_pagto_id", $filtro->forma_pagto_id);
        }
        
        if($filtro->data01){
            if($filtro->data02){
                $retorno->where("data_pagamento",">=", $filtro->data01)->where("data_pagamento","<=", $filtro->data02);
            }else{
                $retorno->where("data_pagamento", $filtro->data01);
            }
        }        
        
        
        return $retorno->get();
    }
}
