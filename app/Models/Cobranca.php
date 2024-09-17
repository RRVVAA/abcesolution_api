<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cobranca extends Model
{
    use HasFactory;
    protected $fillable = [
        'venda_recorrente_id', 'descricao', 'cliente_id', 'status_id', 'valor', 'data_cadastro','data_vencimento',
        'data_pagamento','status_financeiro_id','fin_recebimentos','uuid'
    ];
    
    public function venda(){
        return $this->belongsTo(VendaRecorrente::class, 'venda_recorrente_id');
    }
    public function cliente(){
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }
       
    
    public function status(){
        return $this->belongsTo(Status::class, 'status_id');
    }
    
    public function status_financeiro(){
        return $this->belongsTo(Status::class, 'status_financeiro_id');
    }
    
    public static function atualizarStatus($cobranca_id){
        $cobranca        = Cobranca::find($cobranca_id);        
        $cobranca->status_financeiro_id  = config("constantes.status.ABERTO");
        $parcialmente   = FinContaReceber::where(["status_id" => config("constantes.status.PARCIALMENTE_PAGO"), "cobranca_id" => $cobranca_id ])->first();
        if($parcialmente){
            $cobranca->status_financeiro_id  = config("constantes.status.PARCIALMENTE_PAGO");
        }else{
            $qtde_conta     = FinContaReceber::where("cobranca_id",$cobranca_id )->count("*");
            $pago   = FinContaReceber::where(["status_id" => config("constantes.status.PAGO"), "cobranca_id" => $cobranca_id ])->count("*");
            if($pago == $qtde_conta){
                $cobranca->status_financeiro_id  = config("constantes.status.PAGO");
            }
        }
        $cobranca->save();
    }
}
