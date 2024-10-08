<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProdutoPizza extends Model
{
    protected $fillable = [
        'produto_id', 'tamanho_id', 'valor'
    ];
    
    public function tamanho(){
        return $this->belongsTo(TamanhoPizza::class, 'tamanho_id');
    }
    
    public function produto(){
        return $this->belongsTo(Produto::class, 'produto_id');
    }
    
    public static function tamanhoNaoCadastrado($tamanho, $prod){
        
        foreach($prod->pizza as $pp){
            
            if($pp->tamanho->id == $tamanho){
                return true;
            }
        }
        return false;
    }
}
