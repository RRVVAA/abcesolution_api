<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Orcamento extends Model
{
    use HasFactory;
    protected $fillable = [
        'empresa_id', 'cliente_id', 'usuario_id','frete_id','transportadora_id','status_id','status_financeiro_id',
        'data_orcamento', 'xml_path', 'chave', 'nf', 'situacao','valor_total', 'valor_frete',
        'valor_imposto', 'valor_desconto', 'valor_orcamento','unidade', 'forma_pagamento','enviou_financeiro','enviou_nfe','enviou_estoque',
        'qtde_parcela', 'primeiro_vencimento', 'tPag', 'observacao', 'observacao_interna',
        'pedido_loja_id', 'bandeira_cartao', 'cnpj_cartao', 'cAut_cartao',
        'descricao_pag_outros','total_desconto_item','total_seguro','despesas_outras','desconto_valor','desconto_per', 'venda_id', 'vendedor_id'
    ];
    
    public function duplicatas(){
        return $this->hasMany(FinContaReceber::class, 'orcamento_id', 'id');
    }
       
    
    public function status(){
        return $this->belongsTo(Status::class, 'status_id');
    }
    
    public function venda(){
        return $this->belongsTo(Venda::class, 'venda_id');
    }
    
    
    public function vendedor(){
        return $this->belongsTo(Vendedor::class, 'vendedor_id');
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
        return $this->hasMany(ItemOrcamento::class, 'orcamento_id', 'id');
    }
    
    public static function somarTotal($id){
        $orcamento              = Orcamento::find($id);
        $valor_total            = ItemOrcamento::where("orcamento_id", $id)->sum("subtotal_liquido");
        $orcamento->valor_desconto = 0;
        if($orcamento->desconto_valor > 0){
            $orcamento->valor_desconto = $orcamento->desconto_valor;
        }
        if($orcamento->desconto_per > 0){
            $orcamento->valor_desconto = $orcamento->desconto_per * $valor_total * 0.01;
        }
        
        $orcamento->valor_total = $valor_total;
        $orcamento->valor_orcamento = $valor_total +  $orcamento->valor_frete + $orcamento->despesas_outras + $orcamento->total_seguro - $orcamento->valor_desconto;
        $orcamento->save();
    }
    
 
    
    
    public static function filtro($filtro){
        $retorno = Orcamento::orderBy('orcamentos.data_orcamento', 'asc');
        if($filtro->cliente_id){
            $retorno->where("cliente_id", $filtro->cliente_id);
        }
        
        if($filtro->status_id){
            $retorno->where("status_id", $filtro->status_id);
        }        
       
        
        if($filtro->data2){
            if($filtro->data2){
                $retorno->where("data_orcamento",">=", $filtro->data1)->where("data_orcamento","<=", $filtro->data1);
            }else{
                $retorno->where("data_orcamento", $filtro->data1);
            }
        }
        
        
        return $retorno->get();
    }
}
