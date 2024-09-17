<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ItemVariacaoGrade extends Model
{
    use HasFactory;
    protected $fillable = [
       'empresa_id', 'variacao_grade_id', 'descricao', 'valor', 
    ];
    
    public function variacao(){
        return $this->belongsTo(VariacaoGrade::class, 'variacao_grade_id');
    }
}
