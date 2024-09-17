<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SubSubCategoria extends Model
{
    use HasFactory;
    protected $fillable = ["id","empresa_id", "categoria_id", "subcategoria_id", "subsubcategoria"];
    
    public function empresa(){
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }
    
    public function categoria(){
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }
    
    public function subCategoria(){
        return $this->belongsTo(SubCategoria::class, 'subcategoria_id');
    }
}
