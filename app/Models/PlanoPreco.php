<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanoPreco extends Model
{
    use HasFactory;
    protected $fillable =["plano_id", "recorrencia","preco_de","preco","status_id"];
    
    public function plano(){
        return $this->belongsTo(Plano::class,"plano_id");
    }
    
    public function empresas()
    {
        return $this->hasMany(Empresa::class,"plano_preco_id","id");
    }
    public function status(){
        return $this->belongsTo(Status::class,"status_id","id");
    }
}
