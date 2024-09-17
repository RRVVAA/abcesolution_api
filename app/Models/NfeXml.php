<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NfeXml extends Model
{
    use HasFactory;
    protected  $fillable= [
        'nfe_id',
        'empresa_id',
        'chave',
        'xml',
    ];
    
    public function nfe(){
        return $this->belongsTo(Nfe::class, 'nfe_id');
    }
}
