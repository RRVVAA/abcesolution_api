<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PdvSangria extends Model
{
    use HasFactory;
    protected $fillable = [
        'caixa_id', 'empresa_id', 'usuario_id', 'descricao', 'valor'
    ];
    
    public function caixa(){
        return $this->belongsTo(PdvCaixa::class, 'caixa_id');
    }   
    
    public function usuario(){
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
