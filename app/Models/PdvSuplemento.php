<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PdvSuplemento extends Model
{
    use HasFactory;
    protected $fillable = [
        'caixa_id', 'usuario_id', 'empresa_id', 'descricao', 'valor'
    ];
    
    public function caixa(){
        return $this->belongsTo(PdvCaixa::class, 'caixa_id');
    }
    
    public function usuario(){
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
