<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FinFatura extends Model
{
    use HasFactory;
    protected $fillable =[
        "empresa_id",
        "pagamento_id",
        "recebimento_id",
        "planopreco_id",
        "descricao",
        "forma_pagto_id",
        "status_id",
        "data_emissao",
        "data_vencimento",
        "observacao",
        "valor",
        "status_id",
        "num_fatura",
        "inicio_vigencia",
        "fim_vigencia"
    ];    
    
    public function empresa(){
        return $this->belongsTo(Empresa::class,"empresa_id","id");
    }
    
    public function pagamento(){
        return $this->belongsTo(FinPagamento::class,"pagamento_id");
    }    
    
    public function forma_pagto(){
        return $this->belongsTo(FormaPagto::class,"forma_pagto_id");
    }
    
    public function status(){
        return $this->belongsTo(Status::class,"status_id");
    }
    
    public static function gerarFatura($id_empresa){
        $empresa                = Empresa::find($id_empresa); 
        $ultima_fatura          = FinFatura::where('empresa_id', $id_empresa)->orderBy('id', 'desc')->first();       
        $fat                    = new \stdClass();
        $fat->descricao         = "Fatura do Plano: " . $empresa->planopreco->plano->nome ;
        $fat->empresa_id        = $empresa->id;
        $fat->forma_pagto_id    = $empresa->forma_pagto_id;
        $fat->status_id         = config("constantes.status.ABERTO");
        $fat->data_emissao      = hoje();
        $fat->data_vencimento   = somarData($ultima_fatura->data_vencimento,30* $empresa->planopreco->recorrencia);
        $fat->valor             = $empresa->planopreco->preco;
        $fat->num_fatura        = $ultima_fatura->num_fatura + 1;
        $fat->inicio_vigencia   = $ultima_fatura->data_vencimento;
        $fat->fim_vigencia      = $fat->data_vencimento;
        
        FinFatura::Create(objToArray($fat));       
        
    }
    public static function filtro($filtro){
        $retorno = FinFatura::orderBy('fin_faturas.data_vencimento', 'asc');       
        
        if($filtro->status_id){
            $retorno->where("status_id", $filtro->status_id);
        }
        
        if($filtro->venc01){
            if($filtro->venc02){
                $retorno->where("data_vencimento",">=", $filtro->venc01)->where("data_vencimento","<=", $filtro->venc02);
            }else{
                $retorno->where("data_vencimento", $filtro->venc01);
            }
        }
        
        if($filtro->emissao01){
            if($filtro->emissao02){
                $retorno->where("data_emissao",">=", $filtro->emissao01)->where("data_emissao","<=", $filtro->emissao02);
            }else{
                $retorno->where("data_emissao", $filtro->emissao01);
            }
        }
        
        return $retorno->get();
    }
}
