<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LojaItemPedido extends Model
{
    use HasFactory;
    protected $fillable = [
        'pedido_id', 'produto_id', 'quantidade','valor', 'subtotal','cupom_desconto_id',
        'subtotal_liquido', 'desconto_percentual','desconto_por_valor','desconto_por_unidade',
        'total_desconto_item','unidade','grade_produto_id'
    ];
    
   
    public function pedido(){
        return $this->belongsTo(LojaPedido::class, 'pedido_id');
    }
    
    public function produto(){
        return $this->belongsTo(Produto::class, 'produto_id');
    }
}
