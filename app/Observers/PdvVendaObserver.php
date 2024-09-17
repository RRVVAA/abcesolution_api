<?php

namespace App\Observers;

use App\Models\PdvVenda;
use Illuminate\Support\Str;


class PdvVendaObserver
{
    public function creating(PdvVenda $venda)
    {     
        $venda->uuid = Str::uuid();
    }
    
   
    
}
