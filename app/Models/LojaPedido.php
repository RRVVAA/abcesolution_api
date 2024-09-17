<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LojaPedido extends Model
{
    use HasFactory;
    protected $fillable = [
        'id', 'empresa_id', 'cliente_id', 'endereco_id', 'status_id', 'codigo_rastreio', "pdvvenda_id",
        'valor_venda', 'valor_liquido','valor_frete','desconto_valor','desconto_per','valor_desconto',
        'valor_frete', 'tipo_frete', 'venda_id','numero_nfe', 'observacao', 'rand_pedido',
        'link_boleto', 'qr_code_base64', 'qr_code','transacao_id', 'forma_pagto_id', 'status_pagamento',
        'status_detalhe','hash',"uuid",'estornou_estoque','status_financeiro_id', 'data_pedido', 'status_financeiro_id',
        'status_entrega_id','data_pagamento','data_separacao','data_envio','data_entrega','cupom_desconto_id','valor_desconto_cupom'
        
    ];
    
    public function itens(){
        return $this->hasMany(LojaItemPedido::class, 'pedido_id', 'id');
    }
    
    public function venda(){
        return $this->hasOne(Venda::class, 'pedido_loja_id', 'id');
    }
    
    public function cupom(){
        return $this->belongsTo(CupomDesconto::class, 'cupom_desconto_id', 'id');
    }
    public function empresa(){
        return $this->belongsTo(Empresa::class, 'empresa_id');
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
    
    public function status_entrega(){
        return $this->belongsTo(StatusEntrega::class, 'status_entrega_id');
    }
    public function forma_pagamento(){
        return $this->belongsTo(FormaPagto::class, 'forma_pagto_id');
    }
    
    public function endereco(){
        return $this->belongsTo(EnderecoCliente::class, 'endereco_id');
    }
      
    
    public static function somarTotal($id){
        $venda          = LojaPedido::find($id);
        $valor_venda    = LojaItemPedido::where("pedido_id", $id)->sum("subtotal_liquido");
       
        $venda->valor_desconto_cupom   = LojaItemPedido::where("pedido_id", $id )->sum("total_desconto_item");
        
        
        
        $venda->valor_desconto = 0;
        if($venda->desconto_valor > 0){
            $venda->valor_desconto = $venda->desconto_valor;
        }
        if($venda->desconto_per > 0){
            $venda->valor_desconto = $venda->desconto_per * $valor_venda * 0.01;
        }
        
        $venda->valor_venda = $valor_venda;
        $venda->valor_liquido = $valor_venda +  $venda->valor_frete - $venda->valor_desconto_cupom;
        $venda->save();
    }
    
    
    public function somaItens(){
        $soma = 0;
        foreach($this->itens as $i){
            $soma += $i->quantidade * $i->valor;
        }
        return $soma;
    }
    
    public function somaPeso(){
        $soma = 0;
        foreach($this->itens as $i){            
            $soma += $i->quantidade * $i->produto->peso_bruto;
        }
        return $soma;
    }
    

}
