<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LojaBanner extends Model
{
    use HasFactory;
    protected $fillable = [
        'empresa_id', 'produto_id', 'loja_pacote_id', 'status_id', 'path', 'titulo',
        'descricao'
    ];
    
    public function empresa(){
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }
    
    public function produto(){
        return $this->belongsTo(Produto::class, 'produto_id');
    }
    
}
