<?php

namespace App\Observers;

use App\Models\ItemVendaBalcao;
use App\Models\VendaBalcao;

class ItemVendaBalcaoObserver
{
    public function created(ItemVendaBalcao $item)
    {   
        
        VendaBalcao::somarTotal($item->venda_balcao_id );        
        
    }
    
    public function deleted(ItemVendaBalcao $item)
    {
        VendaBalcao::somarTotal($item->venda_balcao_id );
    }
}
