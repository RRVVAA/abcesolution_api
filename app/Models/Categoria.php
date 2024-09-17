<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{    
    protected $fillable = ["id","empresa_id", "categoria"];
    
    public function empresa(){
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }
    
    public function produtos(){
        return $this->hasMany(Produto::class, 'categoria_id', 'id');
    }
    
    public function subcategorias(){
        return $this->hasMany(SubCategoria::class, 'categoria_id', 'id');
    }
}


