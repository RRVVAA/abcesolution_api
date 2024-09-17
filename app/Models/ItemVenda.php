<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ItemVenda extends Model
{
    use HasFactory;
    protected $fillable = [
        'produto_id', 'venda_id', 'quantidade', 'valor', "subtotal", 'observacao', 'unidade'
    ];
    
    public function produto(){
        return $this->belongsTo(Produto::class, 'produto_id');
    }
    
    public function venda(){
        return $this->belongsTo(Venda::class, 'venda_id');
    }
    
}
