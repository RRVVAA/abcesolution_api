<?php

namespace App\Observers;

use App\Models\LojaItemPedido;
use App\Models\LojaPedido;

class ItemLojaPedidoObserver
{
    public function created(LojaItemPedido $item)
    {   
        
        LojaPedido::somarTotal($item->pedido_id  );        
        
    }
    
    public function deleted(LojaItemPedido $item)
    {
        LojaPedido::somarTotal($item->pedido_id  );
    }
    
}
