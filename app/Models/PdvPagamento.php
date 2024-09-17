<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PdvPagamento extends Model
{
    use HasFactory;
    protected $fillable = [
        'venda_id', 'caixa_id', 'forma_pagto_id', 'num_parcela', "valor", 'subtotal'
    ];
    
    public function caixa(){
        return $this->belongsTo(PdvCaixa::class, 'caixa_id');
    }
    
    public function forma_pagto(){
        return $this->belongsTo(FormaPagto::class, 'forma_pagto_id');
    }
    
    public function venda(){
        return $this->belongsTo(PdvVenda::class, 'venda_id');
    }
}
