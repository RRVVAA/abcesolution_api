<?php

namespace App\Observers;

use App\Models\GradeMovimento;
use App\Models\GradeProduto;

class GradeMovimentoObserver
{
    public function created(GradeMovimento $mov)
    {          
        GradeProduto::atualizarEstoque($mov->grade_id)    ;    
    }
    
    public function updated(GradeMovimento $mov)
    {
        GradeProduto::atualizarEstoque($mov->grade_id)    ;
    }
    public function deleted(GradeMovimento $mov)
    {
        GradeProduto::atualizarEstoque($mov->grade_id)    ; 
    }
}
