<?php

namespace App\Observers;

use App\Models\ItemPedidoDelivery;
use App\Models\PedidoDelivery;

class ItemPedidoDeliveryObserver
{
    public function created(ItemPedidoDelivery $item)
    {   
        
        PedidoDelivery::somarTotal($item->pedido_id  );        
        
    }
    
    public function deleted(ItemPedidoDelivery $item)
    {
        PedidoDelivery::somarTotal($item->pedido_id  );
    }
    
}
