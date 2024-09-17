<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FinContaReceber extends Model
{
    use HasFactory;
    protected $fillable =[
        "empresa_id",
        "usuario_id",
        "descricao",
        "total_juros",
        "total_multa",
        "data_previsao",
        "total_desconto",
        "total_liquido",
        "total_recebido",
        "total_restante",
        "forma_pagto_id",
        "cliente_id",
        "venda_id",
        "pdvduplicata_id",
        "loja_pedido_id",
        "centro_custo_id",
        "forma_pagto_id",
        "num_parcela",
        "ult_parcela",
        "data_emissao",
        "data_vencimento",
        "observacao",
        "valor",
        "status_id",
        "cobranca_id",
        "nfe_id",
        "origem"
    ];
    
 
    
    public function usuario(){
        return $this->belongsTo(User::class, 'usuario_id');
    }
    
    public function status(){
        return $this->belongsTo(Status::class, 'status_id');
    }
    
    public function recebimento(){
        return $this->belongsTo(FinRecebimento::class, 'recebimento_id');
    }
    
    public function venda(){
        return $this->belongsTo(Venda::class, 'venda_id');
    }
    
  
    
    public function cliente(){
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }
   
    public static function atualizar($id){
        $conta          = FinContaReceber::find($id);
        $valor_original = FinRecebimento::where("conta_receber_id", $id)->sum("valor_original");
        $juros          = FinRecebimento::where("conta_receber_id", $id)->sum("juros");
        $multa          = FinRecebimento::where("conta_receber_id", $id)->sum("multa");
        $desconto	    = FinRecebimento::where("conta_receber_id", $id)->sum("desconto");        
        
        $conta->total_juros     = $juros;
        $conta->total_multa     = $multa;
        $conta->total_desconto  = $desconto;
        $conta->total_recebido  = $valor_original;
        
        $conta->total_liquido   = $valor_original +  $conta->total_juros + $conta->total_multa - $conta->total_desconto;
        $conta->total_restante  = $conta->valor - $valor_original ;
        if($conta->total_restante<=0){
            $conta->status_id    = config("constantes.status.PAGO");           
        }
        $conta->save();
    }
    
    public static function filtro($filtro){
        $retorno = FinContaReceber::orderBy('fin_conta_recebers.data_vencimento', 'asc');
        if($filtro->cliente_id){
            $retorno->where("cliente_id", $filtro->cliente_id);
        }
        
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
