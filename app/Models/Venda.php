<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Venda extends Model
{
    use HasFactory;
    protected $fillable = [
        'empresa_id', 'cliente_id','status_financeiro_id', 'usuario_id','frete_id','transportadora_id','status_id',
        'data_venda', 'xml_path', 'chave', 'nf', 'situacao','valor_total', 'valor_frete',
        'valor_imposto', 'valor_desconto', 'valor_venda','forma_pagamento',
        'qtde_parcela', 'primeiro_vencimento', 'tPag', 'observacao', 'observacao_interna',
        'pedido_loja_id', 'bandeira_cartao', 'cnpj_cartao', 'cAut_cartao', 'descricao_pag_outros'
    ];
    
    
    
    public function duplicatas(){
        return $this->hasMany(FinContaReceber::class, 'venda_id', 'id');
    }
    public function status(){
        return $this->belongsTo(Status::class, 'status_id');
    }
    public function empresa(){
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }    
    public function cliente(){
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }
    
   
    public function usuario(){
        return $this->belongsTo(User::class, 'usuario_id');
    }
    
    public function itens(){
        return $this->hasMany(ItemVenda::class, 'venda_id', 'id');
    }
    
    public function qtde_paga(){
        return FinContaReceber::where("recebimento_id",">",0)->where("venda_id", $this->id)->count();
    }
    
    public function foiPaga(){
        return ($this->qtde_paga() >= count($this->duplicatas));
    }
    
    public static function atualizarStatus($venda_id){
        $venda        = Venda::find($venda_id);
        $venda->status_financeiro_id  = config("constantes.status.ABERTO");
        $parcialmente   = FinContaReceber::where(["status_id" => config("constantes.status.PARCIALMENTE_PAGO"), "venda_id" => $venda_id ])->first();
        if($parcialmente){
            $venda->status_financeiro_id  = config("constantes.status.PARCIALMENTE_PAGO");
        }else{
            $qtde_conta     = FinContaReceber::where("venda_id",$venda_id )->count("*");
            $pago   = FinContaReceber::where(["status_id" => config("constantes.status.PAGO"), "venda_id" => $venda_id ])->count("*");
            if($pago == $qtde_conta){
                $venda->status_financeiro_id  = config("constantes.status.PAGO");
            }
        }
        $venda->save();
    }

    
}
