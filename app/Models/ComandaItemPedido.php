<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComandaItemPedido extends Model
{
    use HasFactory;

    protected $fillable = [
        'produto_id',
        "pedido_id",
        'opcao_id',
        'opcao_item_id',
        'quantidade',
        'valor',
        'subtotal',
        'status_id',
    ];

    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }

    public function pedido()
    {
        return $this->belongsTo(ComandaPedido::class);
    }

    public function opcao()
    {
        return $this->belongsTo(DeliveryOpcao::class, 'opcao_id');
    }

    public function opcaoItem()
    {
        return $this->hasMany(DeliveryOpcaoItem::class, 'opcao_item_id', 'id');
    }

    public function opcoes()
    {
        return $this->hasMany(DeliveryOpcaoEscolhida::class, 'item_pedido_id', 'id');
    }


}
