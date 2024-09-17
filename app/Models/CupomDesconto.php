<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CupomDesconto extends Model
{
    use HasFactory;
    protected $fillable=["empresa_id","produto_id","categoria_id","ativo", "codigo","valor_minimo",
                         "descricao","desconto_por_valor","desconto_percentual","data_validade","qtde_limite"];
    
    public function produto(){
        return $this->belongsTo(Produto::class, 'produto_id');
    }
    
    public function categoria(){
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }
}
