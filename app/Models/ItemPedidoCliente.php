<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemPedidoCliente extends Model
{
    use HasFactory;
    protected $fillable = [
        "id",
        'produto_id',
        'pedido_id',
        "qtde",
        "valor",
        "origem"];
    
    public function produto(){
        return $this->belongsTo(Produto::class,"produto_id","id");
    }
    
    public function pedido(){
        return $this->belongsTo(PedidoCliente::class,"pedido_id","id");
    }
    
}
