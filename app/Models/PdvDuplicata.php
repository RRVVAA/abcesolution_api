<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PdvDuplicata extends Model
{
    use HasFactory;
    protected $fillable = [
        'venda_id',
        'transacao_id',
        'caixa_id',
        'nDup',
        'tPag',
        'indPag',
        'dVenc',
        'vDup',
        'obs'
    ];
    
    public function venda(){
        return $this->belongsTo(PdvVenda::class, 'venda_id');
    }
    
    public function forma_pagto(){
        return $this->belongsTo(FormaPagto::class,'tPag');
    }
}
