<?php

namespace App\Observers;


use App\Models\FinContaReceber;
use App\Models\FinRecebimento;

class ContaReceberObserver
{
    
    public function deleted(FinContaReceber $contareceber)
    {
        //FinRecebimento::where("conta_receber_id", $contareceber->id)->delete();
    }
   
}
