<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CertificadoDigital extends Model
{
    use HasFactory;
    protected $fillable =[
        'empresa_id',
        'emitente_id',        
        'certificado_nome_arquivo',
        'certificado_arquivo_binario',
        'certificado_senha'
    ]; 
    
    
    public function emitente(){
        return $this->belongsTo(Emitente::class, 'emitente_id');
    }
    
    public function empresa(){
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }
}
