<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdutoOs extends Model
{
    use HasFactory;
    protected $fillable = [
        'produto_id', 'os_id', 'unidade', 'quantidade', 'valor','desconto_por_item',
        'total_desconto_item', "subtotal", 'observacao','subtotal_liquido'
    ];
    
    public function produto(){
        return $this->belongsTo(Produto::class, 'produto_id');
    }
    
    public function os(){
        return $this->belongsTo(OrdemServico::class, 'os_id');
    }
}
