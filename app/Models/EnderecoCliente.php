<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EnderecoCliente extends Model
{
    use HasFactory;
    protected $fillable = [
       'id', 'empresa_id', 'cliente_id', 'logradouro', 'numero', 'bairro', 'cidade','uf','status_id','cep','complemento','ibge'
    ];
    
    public function empresa(){
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }
    public function cliente(){
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }
    
}
