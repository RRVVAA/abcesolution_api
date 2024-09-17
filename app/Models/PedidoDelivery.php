<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PedidoDelivery extends Model
{
    protected $fillable = [
        'id', 'empresa_id', 'cliente_id', 'endereco_id', 'status_id', 'codigo_rastreio', "pdvvenda_id",
        'valor_venda', 'valor_liquido','valor_frete','desconto_valor','desconto_per','valor_desconto',
        'valor_frete', 'tipo_frete', 'venda_id','numero_nfe', 'observacao', 'rand_pedido',
        'link_boleto', 'qr_code_base64', 'qr_code','transacao_id', 'forma_pagto_id', 'status_pagamento',
        'status_detalhe','hash',"uuid",'estornou_estoque','status_financeiro_id', 'data_pedido', 'status_financeiro_id',
        'status_entrega_id','data_pagamento','data_separacao','data_envio','data_entrega','cupom_desconto_id','valor_desconto_cupom'
        
    ];
    
    public function itens(){
        return $this->hasMany(ItemPedidoDelivery::class, 'pedido_id', 'id');
    }
    
    public function cliente(){
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }
    
    public function status(){
        return $this->belongsTo(Status::class, 'status_id');
    }
    
  
    
    public function endereco(){
        return $this->belongsTo(EnderecoCliente::class, 'endereco_id');
    }
    
   
    
    public static function somarTotal($id){
        $venda          = PedidoDelivery::find($id);
        $valor_venda    = ItemPedidoDelivery::where("pedido_id", $id)->sum("subtotal_liquido");
        
        $venda->valor_desconto_cupom   = ItemPedidoDelivery::where("pedido_id", $id )->sum("total_desconto_item");
        
        
        
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
        
        $config = DeliveryConfig::first();
        $total = 0;
        
        if($this->valor_total > 0){
            return $this->valor_total;
        } else{
            
            foreach($this->itens as $i){
                
                if(count($i->sabores) > 0){
                    $maiorValor = 0;
                    $somaValores = 0;
                    foreach($i->sabores as $sb){
                        $v = $sb->maiorValor($sb->sabor_id, $i->tamanho_id);
                        $somaValores += $v;
                        if($v > $maiorValor) $maiorValor = $v;
                    }
                    if(env("DIVISAO_VALOR_PIZZA") == 1){
                        $maiorValor = number_format(($somaValores/sizeof($i->sabores)),2);
                    }
                    $total += $i->quantidade * $maiorValor;
                }else{
                    $total += $i->quantidade * $i->produto->valor;
                }
                
                foreach($i->itensAdicionais as $a){
                    $total += $a->quantidade * $a->adicional->valor;
                }
                
            }
            
            if($this->cupom_id != null){
                $total -= $this->desconto;
            }
            
            if($this->endereco_id != null)
                $total += $config != null ? $config->valor_entrega : 0;
                return $total;
        }
    }
    
    public function somaItensSemFrete(){
        $total = 0;
        foreach($this->itens as $i){
            
            if(count($i->sabores) > 0){
                $maiorValor = 0;
                $somaValores = 0;
                foreach($i->sabores as $sb){
                    $v = $sb->maiorValor($sb->sabor_id, $i->tamanho_id);
                    $somaValores += $v;
                    if($v > $maiorValor) $maiorValor = $v;
                }
                if(env("DIVISAO_VALOR_PIZZA") == 1){
                    $maiorValor = number_format(($somaValores/sizeof($i->sabores)),2);
                }
                $total += $i->quantidade * $maiorValor;
            }else{
                $total += $i->quantidade * $i->produto->valor;
            }
            
            foreach($i->itensAdicionais as $a){
                $total += $a->quantidade * $a->adicional->valor;
            }
            
        }
        return $total;
    }
    
    public function somaCarrinho(){
        $config = DeliveryConfig::first();
        $total = 0;
        if($this->valor_total == 0){
            foreach($this->itens as $i){
                if(count($i->sabores) > 0){
                    $maiorValor = 0;
                    foreach($i->sabores as $sb){
                        $sb->produto->produto;
                        $v = $sb->maiorValor($sb->sabor_id, $i->tamanho_id);
                        if($v > $maiorValor) $maiorValor = $v;
                    }
                    
                    $total += $i->quantidade * $maiorValor;
                }else{
                    $total += $i->quantidade * $i->produto->valor;
                }
                foreach($i->itensAdicionais as $a){
                    $total += $a->quantidade * $a->adicional->valor;
                }
            }
        }
        
        return $total;
    }
    
    public function calculaFrete(){
        return $this->valor_total - $this->somaItensSemFrete() + $this->desconto;
    }
    
    public function itensOrdenadosPorPizza(){
        $temp = [];
        foreach($this->itens as $i){
            if(count($i->sabores) > 0){
                array_push($temp, $i);
            }
        }
        
        foreach($this->itens as $i){
            if(count($i->sabores) == 0){
                array_push($temp, $i);
            }
        }
        return $temp;
    }
}
