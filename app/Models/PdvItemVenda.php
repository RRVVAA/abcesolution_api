<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PdvItemVenda extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'produto_id',  'venda_id', 'grade_produto_id', 'qtde', 'valor', "subtotal", 'observacao','desconto_item','subtotal_liquido',
        'desconto_percentual', 'desconto_por_valor', 'desconto_por_unidade', 'total_desconto_item','cupom_desconto_id'
    ];
    
    public function produto(){
        return $this->belongsTo(Produto::class, 'produto_id');
    }
    
    public function venda(){
        return $this->belongsTo(PdvVenda::class, 'venda_id');
    }
}
