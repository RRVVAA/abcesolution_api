<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SubCategoria extends Model
{
    use HasFactory;
    protected $fillable = ["id","empresa_id", "categoria_id","subcategoria"];
    
    public function empresa(){
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }
    
    public function categoria(){
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }
    
    public function subsubcategorias(){
        return $this->hasMany(SubSubCategoria::class, 'subcategoria_id', 'id');
    }
}
