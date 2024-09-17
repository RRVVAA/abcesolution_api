<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemVendaBalcao extends Model
{
    use HasFactory;
    protected $fillable = [
        'produto_id', 'venda_balcao_id', 'unidade', 'quantidade', 'valor','desconto_percentual',
        'desconto_por_valor', "subtotal", 'observacao','subtotal_liquido','desconto_por_unidade','total_desconto_item'
    ];
    
    public function produto(){
        return $this->belongsTo(Produto::class, 'produto_id');
    }
    
    public function venda(){
        return $this->belongsTo(VendaBalcao::class, 'venda_balcao_id');
    }
}
