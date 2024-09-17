<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PdvCaixa extends Model
{
    use HasFactory;
    protected $fillable = [
        'empresa_id', 'caixanumero_id', 'data_abertura', 'hora_abertura', 'valor_abertura', 'gerente_abriu_id', 
        'gerente_fechou_id','data_fechamento', 'hora_fechamento', 'valor_fechamento', 'valor_vendido', 'valor_quebra',
        'valor_sangria', 'valor_suplemento',  'total_em_caixa', 'usuario_abriu_id',
        'usuario_fechou_id', 'status_id' ,'dinheiro_gaveta'       
    ];
    
    public function empresa(){
        return $this->belongsTo(Empresa::class,"empresa_id","id");
    }
    
    
    public function num_pdv(){
        return $this->belongsTo(PdvCaixaNumero::class,"caixanumero_id","id");
    }
    
    public function status(){
        return $this->belongsTo(Status::class,"status_id","id");
    }
    
    public function usuario(){
        return $this->belongsTo(User::class,"usuario_abriu_id","id");
    }
    
    public static function atualizar($caixa_id){
        $caixa              = PdvCaixa::find($caixa_id);
        
        //Calcula o total
        $total_venda        = PdvVenda::where("caixa_id", $caixa->id)->sum("valor_liquido");
        $total_sangria      = PdvSangria::where("caixa_id", $caixa->id)->sum("valor");
        $total_suplemento   = PdvSuplemento::where("caixa_id", $caixa->id)->sum("valor");
        $pagamento_dinheiro = PdvDuplicata::where(["caixa_id" => $caixa->id, "tPag" =>config("constantes.forma_pagto.DINHEIRO")])->sum("vDup");
        
        $caixa->valor_vendido       = $total_venda;
        $caixa->valor_sangria       = $total_sangria;
        $caixa->valor_suplemento    = $total_suplemento;
        $caixa->dinheiro_gaveta     = $caixa->valor_abertura - $caixa->valor_sangria + $caixa->valor_suplemento + $pagamento_dinheiro ;
        $caixa->total_em_caixa      = $caixa->valor_abertura + $caixa->valor_vendido - $caixa->valor_sangria + $caixa->valor_suplemento;
        $caixa->save();
        
    }
    
    public static function caixaNumeroNaoInseridas($id){
        $numeros = PdvCaixaNumero::where("empresa_id", $id)-> whereNotIn('id', function($query) {
            $query->select('pdv_caixas.caixanumero_id');
            $query->from('pdv_caixas');
            $query->whereRaw("pdv_caixas .status_id={config('constantes.status.ABERTO')}");
        })
        ->get();
        return $numeros;
    }
}
