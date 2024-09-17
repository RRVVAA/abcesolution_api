<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NfceXml extends Model
{
    use HasFactory;
    protected  $fillable= [
        'nfce_id',
        'empresa_id',
        'chave',
        'xml',
    ];
    
    public function nfce(){
        return $this->belongsTo(Nfce::class, 'nfce_id');
    }
}
