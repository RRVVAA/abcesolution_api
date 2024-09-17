<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Tenant\Traits\EmpresaTrait;

class ContaCorrente extends Model
{
    use HasFactory;
    protected $fillable = ["id","empresa_id","descricao", "banco_id", "agencia","conta","tipo_conta_corrente_id","pix", "saldo_inicial"];
    
    public function empresa(){
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }
    
    public function tipo(){
        return $this->belongsTo(TipoContaCorrente::class, 'tipo_conta_corrente_id');
    }
    
    public function banco(){
        return $this->belongsTo(Banco::class, 'banco_id');
    }
}
